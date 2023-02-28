<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{config('app.name','Dashboard')}}</title>
    <link rel="stylesheet" type="text/css" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.13/css/jquery.dataTables.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.6.0.slim.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>



</head>
<body class="main-content">


        <div class="container mt-5 table-responsive ">
          <div class="navbar navbar-expand-md navbar-light bg-white shadow">
            <div class="container-fluid">
                <h2 class="resize-text">    
                    LOVE FESTIVAL NAIROBI
                </h2>
            </div>
        </div>
            <div class="form-group row mt-3">
                <div class="col-md-10">
                    <form action="/search" method="get">
                        <div class="row">
                            <div class="col-md-10">
                                <input type="text" name="search" class="form-control mt-2" placeholder="Search by column name">
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-primary w-100 mt-2">Search</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-2 mt-3 mt-md-0">
                    <button class="btn btn-success w-100 mt-2" data-toggle="modal" data-target="#exportModal">Export</button>

                </div>
            </div>
            <table id="regdata" class="table table-striped table-bordered table-responsive-sm">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Name</th>
                        <th>Sub-county</th>
                        <th>Church Name</th>
                        <th>Mobile</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($event as $row)
                    <tr>
                        <td>{{$row->id}}</td>
                        <td>{{$row->name}}</td>
                        <td>{{$row->Sub_County}}</td>
                        <td>{{$row->Church_Name}}</td>
                        <td>{{$row->mobile}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="d-flex justify-content-center">
                {!! $event->links() !!}
            </div>
        </div>
   
<!-- Modal for Export Data -->
<div class="modal fade" id="exportModal" tabindex="-1" role="dialog" aria-labelledby="exportModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exportModalLabel">Export Data</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <form id="exportForm" method="POST" action="/export">
                @csrf
        
            <div class="form-group">
              <label for="startDate">Start Date:</label>
              <input type="datetime-local" class="form-control" id="startDate" name="startDate">
            </div>
            <div class="form-group">
              <label for="endDate">End Date:</label>
              <input type="datetime-local" class="form-control" id="endDate" name="endDate">
            </div>
            <div class="form-group">
              <button type="submit" class="btn btn-primary">Export</button>
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  

    
</body>
</html>
