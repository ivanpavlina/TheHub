<?php

if (!defined("ADMIN_LAYOUT")) {
  ob_start();
  header('Location: /index.php');
  ob_end_flush();
  die();
}

?>
<div class="container">
  <div class="row">
    <div class="col-lg-12">
      <h1 class="page-header">Network Monitoring</h1>
    </div>
    <div class="col-lg-12">
      <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="heading-live-charts"> 
          <h4 class="panel-title">
            <i class="fa fa-dashboard"></i>
            <a role="button" data-toggle="collapse" href="#collapse-live-charts" aria-expanded="true" aria-controls="collapse-live-charts" class="trigger">
            Live Stats
            </a>
          </h4>
        </div>
        <div id="collapse-live-charts" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="heading-live-charts">
          <div class="panel-body">
            <div class="row">
              <div class="col-lg-12">
                <div class="pull-left">
                  <label for="charts-live-type-selector">Data type </label>
                  <select id="charts-live-type-selector">
                    <option value="download">Download</option>
                    <option value="upload">Upload</option>
                    <option value="local">LAN</option>
                  </select>
                </div>
              </div>
              <div class="col-lg-12">
                <div class="row">
                  <div class="col-lg-6">
                    <div class="row">
                      <div class="col-xs-12">
                        <p class="text-center"><b>Traffic</b></p>
                      </div>
                      <div class="col-xs-12">
                        <canvas id="traffic-live-chart-canvas"></canvas>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-6">
                    <div class="row">
                      <div class="col-xs-12">
                        <p class="text-center"><b>Packets</b></p>
                      </div>
                      <div class="col-xs-12">
                        <canvas id="packets-live-chart-canvas"></canvas>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-12 text-center">
                <p>Showing last 10 minutes</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-12">
      <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="heading-hosts">
          <p class="panel-title">
            <i class="fa fa-cubes fa-fw"></i>
            <a role="button" data-toggle="collapse" href="#collapse-hosts" aria-expanded="true" aria-controls="collapse-hosts" class="trigger">
            Hosts
            </a>
          </p>
        </div>
        <div id="collapse-hosts" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="heading-hosts">
          <div id="hosts-container" class="panel-body">
          </div>
        </div>
      </div>
    </div>

      <div class="col-lg-12">
      <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="heading-historic-charts"> 
          <h4 class="panel-title">
            <i class="fa fa-dashboard"></i>
            <a role="button" data-toggle="collapse" href="#collapse-historic-charts" aria-expanded="true" aria-controls="collapse-historic-charts" class="trigger">
            Historic Stats
            </a>
            <i id="historic-charts-loading-icon" class="fa fa-spinner fa-spin pull-right hidden"></i>
          </h4>
        </div>
        <div id="collapse-historic-charts" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-historic-charts">
          <div class="panel-body">
            <div class="row">
              <div class="col-lg-12">
                <div class="pull-left">
                  <label for="charts-historic-type-selector">Data type</label>
                  <select id="charts-historic-type-selector">
                    <option value="download">Download</option>
                    <option value="upload">Upload</option>
                    <option value="local">LAN</option>
                  </select>
                  </br>
                  <label for="charts-historic-time-selector">Time select</label>
                  <select id="charts-historic-time-selector">
                    <option value="1440">1 day</option>
                    <option value="2880">2 days</option>
                    <option value="14400">10 days</option>
                    <option value="43200">1 month</option>
                  </select>
                </div>
              </div>
              <div class="col-lg-12">
                <div class="row">
                  <div class="col-lg-6">
                    <div class="row">
                      <div class="col-xs-12">
                        <p class="text-center"><b>traffic</b></p>
                      </div>
                      <div class="col-xs-12">
                        <canvas id="traffic-historic-chart-canvas"></canvas>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-6">
                    <div class="row">
                      <div class="col-xs-12">
                        <p class="text-center"><b>Packets</b></p>
                      </div>
                      <div class="col-xs-12">
                        <canvas id="packets-historic-chart-canvas"></canvas>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>