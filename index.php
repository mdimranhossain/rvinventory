<?php

/**

* Index

*

* @Package: rvinventory

**/



declare(strict_types=1);



$viAutoload = dirname(__FILE__) . '/vendor/autoload.php';



if (file_exists($viAutoload)) {

    require_once $viAutoload;

}



use Inc\VehicleData;



$viRemoteUrl = 'https://jsonplaceholder.typicode.com/users/';



$viData = new VehicleData($viRemoteUrl);



$viVehicles = $viData->vehicleList();



$viVehicles = json_decode($viVehicles);



?>



<!DOCTYPE html>

<html>



<head>

  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Vehicles</title>

  <link rel="stylesheet" type="text/css" href="assets/datatables.min.css" />

  <link rel="stylesheet" type="text/css" href="assets/styles.css" />

  <script type="text/javascript" src="assets/jquery.min.js"></script>

  <script type="text/javascript" src="assets/datatables.min.js"></script>



</head>



<body>

  <div id="rvinventory">

    <h2>Vehicles</h2>

    <div id="vehiclelist" class="table-responsive">

      <table id="vehicles" class="display table table-bordered table-striped">

        <thead>

          <tr>

            <th>ID</th>

            <th>Name</th>

            <th>Vehiclename</th>

            <th>Email</th>

            <th>Phone</th>

            <th>Website</th>

          </tr>

        </thead>

        <tbody>

          <?php

            if ($viVehicles) {

                foreach ($viVehicles as $viVehicle) {

                    echo '<tr><td><a class="dlink" dataid="' . $viVehicle->id . '" href="#" data-toggle="modal" data-target="#vehicledetails">' . $viVehicle->id . '</a></td><td><a class="dlink" dataid="' . $viVehicle->id . '" href="#" data-toggle="modal" data-target="#vehicledetails">' . $viVehicle->name . '</a></td><td><a class="dlink" dataid="' . $viVehicle->id . '" href="#" data-toggle="modal" data-target="#vehicledetails">' . $viVehicle->vehiclename . '</a></td><td>' . $viVehicle->email . '</td><td>' . $viVehicle->phone . '</td><td>' . $viVehicle->website . '</td></tr>';

                }

            }

            ?>

        </tbody>



        <tfoot>

          <tr>

            <th>ID</th>

            <th>Name</th>

            <th>Vehiclename</th>

            <th>Email</th>

            <th>Phone</th>

            <th>Website</th>

          </tr>

        </tfoot>

      </table>

    </div>

    <div id="vehicledetails" class="modal fade" role="dialog">

      <div class="modal-dialog">

        <!-- Modal content-->

        <div class="modal-content">

          <div class="modal-header">

            <button type="button" class="close" data-dismiss="modal">&times;</button>

            <h2 class="modal-title">Vehicle Details</h2>

          </div>

          <div class="modal-body">

            <div id="details" class="table-responsive" style="width:100%; max-width:540px; margin:0px auto;">



            </div>

          </div>

        </div>



      </div>

    </div>

  </div>

  <script>

    jQuery(document).ready(function($) {

      $('#vehicles').DataTable({

        responsive: true,

      });



      $(document).on('click', '.dlink', function(e) {

        e.preventDefault();

        var id = $(this).attr('dataid');



        $.ajax({

            dataType: "json",

            url: "./endpoint.php?id=" + id,

          })

          .done(function(data) {

            var vehicle = '';

            vehicle += '<table class="table table-bordered table-striped" border="1">';

            vehicle += '<tr><td>ID:</td><td>' + data.id + '</td></tr>';

            vehicle += '<tr><td>Name:</td><td>' + data.name + '</td></tr>';

            vehicle += '<tr><td>VehicleName:</td><td>' + data.vehiclename + '</td></tr>';

            vehicle += '<tr><td>Email:</td><td>' + data.email + '</td></tr>';

            vehicle += '<tr><td>Phone:</td><td>' + data.phone + '</td></tr>';

            vehicle += '<tr><td>Website:</td><td>' + data.website + '</td></tr>';

            vehicle += '<tr><td>Address:</td><td>Street- ' + data.address.street + '<br>Suite- ' + data.address.suite + '<br>City- ' + data.address.city + '<br>ZipCode- ' + data.address.zipcode + '<br>Latitude- ' + data.address.geo.lat + '<br>' + 'Longitude- ' + data.address.geo.lng + '</td></tr>';

            vehicle += '<tr><td>Company:</td><td>Name- ' + data.company.name + '<br>catchPhrase- ' + data.company.catchPhrase + '<br>bs- ' + data.company.bs + '</td></tr>';

            vehicle += '</table>';

            $('#details').html(vehicle);

            console.log(data);

          });



      });

    });

  </script>

  <div style="display: block; clear: both;"></div>

</body>



</html>

