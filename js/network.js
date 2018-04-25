var hosts_update_interval = 10000;
var charts_update_interval = 5000;

var charts_interval;
var hosts_interval;

var traffic_live_chart;
var packet_live_chart;
var traffic_historic_chart
var packet_historic_chart;

$(document).ready(function(){
    init_templates();

    initialize_live_charts();
    initialize_hosts();

    $("#charts-live-type-selector").on('change', function() {
        initialize_live_charts();
    });

    initialize_historic_charts();
    $("#charts-historic-type-selector").on('change', function() {
        initialize_historic_charts();
    });
    $("#charts-historic-time-selector").on('change', function() {
        initialize_historic_charts();
    });
});

function init_templates() {
    element = '\
    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12">\
      <div class="card" style="background-color:{{:color}}; {{if active}} opacity:1; {{else}} opacity:0.6; {{/if}}" ">\
          <div class="card-block">\
            <div class="form-inline">\
              <h4 class="card-title" style="float:left;">{{:name}}</h4>\
              <span class="fa {{if active}} fa-chain {{else}} fa-chain-broken {{/if}}" style="float:right;"> </span>\
            </div>\
            <div class="card-text">\
              <p>{{:ip_address}}</p>\
              <p>{{:mac_address}}</p>\
            </div>\
          </div>\
      </div>\
    </div>';
    $.templates('hostBox', element);
}

function initialize_hosts() {
    $.ajax({                                      
        type: "GET",
        url: 'api/network.php',
        data: "request=host_list",
        dataType: 'json',
        success: function(response) {
            $(function() {
                var hosts = [];
                $.each(response, function(i, host) {
                    item = $.render.hostBox(host);
                    hosts[i] = item;
                });
                $("#hosts-container").html(hosts);
            });
        } 
    });
    setTimeout(initialize_hosts, hosts_update_interval);
}

function initialize_live_charts() {
    selected_type = $("#charts-live-type-selector").val();

    if (charts_interval !== undefined) {
        clearInterval(charts_interval);
    }

    $.ajax({
        type: "GET",
        url: 'api/network.php',
        data: "request=setup_live_traffic_data&type="+selected_type,
        dataType: 'json',
        success: function(response) {
            if (traffic_live_chart === undefined) {
                traffic_live_chart = new Chart(document.getElementById("traffic-live-chart-canvas"), {
                    type: 'line',
                    options: {
                        responsive: true,
                        scales: {
                            xAxes: [{
                                display: true,
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Time'
                                }
                            }],
                            yAxes: [{
                                display: true,
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Mb/s'
                                }
                            }]
                        }
                    }
                });
            }
            if (packet_live_chart === undefined) {
                packet_live_chart = new Chart(document.getElementById("packets-live-chart-canvas"), {
                    type: 'line',
                    options: {
                        responsive: true,
            
                        scales: {
                            xAxes: [{
                                display: true,
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Time'
                                }
                            }],
                            yAxes: [{
                                display: true,
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Packets'
                                }
                            }]
                        }
                    }
                });
            }

            traffic_live_chart.data.datasets = response.datasets.traffic;
            packet_live_chart.data.datasets = response.datasets.packets;
            traffic_live_chart.data.labels = response.labels;
            packet_live_chart.data.labels = response.labels;
            traffic_live_chart.update();
            packet_live_chart.update();

            charts_interval = setInterval(update_live_charts, charts_update_interval);

        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            // Error init char, set timeout to retry
            setTimeout(initialize_live_charts, charts_update_interval);
        }  
    });
};

function update_live_charts() {
    if (charts_interval !== undefined) {
        clearInterval(charts_interval);
    }

    selected_type = $("#charts-live-type-selector").val();
    //selected_time = $("#charts-time-selector").val();

    $.ajax({
        type: "GET",
        url: 'api/network.php',
        data: "request=update_traffic_data&type="+selected_type,
        dataType: 'json',
        success: function(response) {
            //traffic
            // If not all hosts are initialized in graph reload it
            for (var ip in response.datasets.traffic) {
                found = false;

                traffic_live_chart.data.datasets.forEach((dataset) => {
                    if (dataset.ip_address == ip) {
                        found = true;
                    }
                });

                if (found == false) {
                    console.log("New IP found - " + ip)
                    return initialize_live_charts();
                    break;
                }
            }

            last_label = traffic_live_chart.data.labels[traffic_live_chart.data.labels.length-1];
            if (last_label != response.label) {
                traffic_live_chart.data.labels.push(response.label);
                traffic_live_chart.data.datasets.forEach((dataset) => {
                    var traffic_count = response.datasets.traffic[dataset.ip_address];
                    if (traffic_count != null){
                        dataset.data.push(traffic_count);
                    } else {
                        dataset.data.push(0);
                    }
                });

                //packet_live_chart.data.labels.push(response.label);
                packet_live_chart.data.datasets.forEach((dataset) => {
                    var packet_count = response.datasets.packets[dataset.ip_address];
                    if (packet_count != null){
                        dataset.data.push(packet_count);
                    } else {
                        dataset.data.push(0);
                    }
                });

                //traffic_live_chart.data.labels.shift();
                traffic_live_chart.data.datasets.forEach((dataset) => {
                    dataset.data.shift();
                });

                packet_live_chart.data.labels.shift();
                packet_live_chart.data.datasets.forEach((dataset) => {
                    dataset.data.shift();
                });

                traffic_live_chart.update();
                packet_live_chart.update();

                charts_interval = setInterval(update_live_charts, charts_update_interval);
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            setTimeout(initialize_live_charts, charts_update_interval);
        }
    });
};

function initialize_historic_charts() {
    $('#historic-charts-loading-icon').removeClass('hidden');

    selected_type = $("#charts-historic-type-selector").val();
    selected_time = $("#charts-historic-time-selector").val();

    $.ajax({
        type: "GET",
        url: 'api/network.php',
        data: "request=setup_historic_traffic_data&type="+selected_type+"&time="+selected_time,
        dataType: 'json',
        success: function(response) {
            if (traffic_historic_chart === undefined) {
                traffic_historic_chart = new Chart(document.getElementById("traffic-historic-chart-canvas"), {
                    type: 'line',
                    options: {
                        responsive: true,
                        scales: {
                            xAxes: [{
                                display: true,
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Time'
                                }
                            }],
                            yAxes: [{
                                display: true,
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Mb/s'
                                }
                            }]
                        }
                    }
                });
            }
            if (packet_historic_chart === undefined) {
                packet_historic_chart = new Chart(document.getElementById("packets-historic-chart-canvas"), {
                    type: 'line',
                    options: {
                        responsive: true,
            
                        scales: {
                            xAxes: [{
                                display: true,
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Time'
                                }
                            }],
                            yAxes: [{
                                display: true,
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Packets'
                                }
                            }]
                        }
                    }
                });
            }

            traffic_historic_chart.data.datasets = response.datasets.traffic;
            packet_historic_chart.data.datasets = response.datasets.packets;
            traffic_historic_chart.data.labels = response.labels;
            packet_historic_chart.data.labels = response.labels;
            traffic_historic_chart.update();
            packet_historic_chart.update();
            $('#historic-charts-loading-icon').addClass('hidden');
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            $('#historic-charts-loading-icon').addClass('hidden');
            // Error init char, set timeout to retry
            setTimeout(initialize_historic_charts, charts_update_interval);
        }  
    });
};

