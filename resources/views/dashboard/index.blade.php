
@extends('layouts.mainLayout')

<div class="navigation container-fluid bg-dark">
  <nav class="navbar">
    <div class="button-container">
      <button class="btn btn-info" onclick="window.location.href='{{ url('/') }}'">Back</button>
      <button class="btn btn-danger" onclick="window.location.href='{{ url('/logout') }}'">Logout</button>
    </div>
  </nav>
</div>


        <div class="container mt-5 table-responsive ">
          <div class="navbar navbar-expand-md navbar-light bg-white shadow">
            <div class="container-fluid d-flex justify-content-center align-items-center">
                <h2 class="resize-text">    
                    LOVE FESTIVAL NAIROBI
                </h2>

                 


            </div>

        </div>
            <div class="form-group row mt-3">
                <div class="col-md-10">
                    <form action="/search" method="post">

                 @csrf

                        <div class="row">
                            <div class="col-md-10">
                                <input type="text" name="search" class="form-control mt-2" placeholder="Search by column name">
                            </div>
                            <div class="col-md-2 mt-2">
                                <button class="btn btn-info w-100 ">Search</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-2 mt-3 mt-md-0 exportcontainer 
                ">
                    <button class="btn btn-success w-100 mt-2 mb-3" data-toggle="modal" data-target="#exportModal">Export</button>

                </div>
            </div>
            <table id="regdata" class="table table-striped table-bordered table-responsive-sm">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Name</th>
                      <th>Sub-Zones</th>

                        <th>Church Name</th>
                        <th>Mobile</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($event as $row)
                    <tr>
                        <td>{{$row->id}}</td>
                        <td>{{$row->name}}</td>
                        <td>{{$row->zoneName}}</td>
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
  

    

