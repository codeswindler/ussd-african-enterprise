<?php

namespace App\Http\Controllers;


use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Models\eventRegistration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
  //

  public function index()
  {
    $event = eventRegistration::where('status', 1)
      ->orderByDesc('event_registrations.id')
      ->join('zones', 'event_registrations.Sub_County', '=', 'zones.zoneId')
      ->select('event_registrations.*', 'zones.zoneName')
      ->paginate(5);

    return view('dashboard.index', compact('event'));
  }

  public function search(Request $request)
  {

    $search = $request->input('search');

    if (empty($search)) {

      return $this->index();

  }
  

    $event = DB::table('event_registrations')
      ->join('zones', 'event_registrations.Sub_County', '=', 'zones.zoneId')
      ->select('event_registrations.*', 'zones.zoneName')
      ->where('mobile', 'like', "%$search%")
      ->orWhere('event_registrations.id', 'like', "%$search%")
      ->orWhere('name', 'like', "%$search%")
      ->orWhere('Church_Name', 'like', "%$search%")
      ->orWhere('zones.zoneName', 'like', "%$search%")
      ->paginate(5);
      

    return view('dashboard.index', compact('event'));
  }

  public function exportData(Request $request)
  {
    $startDate = Carbon::createFromFormat('Y-m-d\TH:i', $request->input('startDate'))->format('Y-m-d H:i:s');
    $endDate = Carbon::createFromFormat('Y-m-d\TH:i', $request->input('endDate'))->format('Y-m-d H:i:s');


    $data = DB::table('event_registrations')
      ->whereBetween('created_at', [$startDate, $endDate])
      ->get();


    $data_array = json_decode($data, true);

    $file_path = storage_path('logs/output.csv');
    $file_handle = fopen($file_path, 'w');

    if (!empty($data_array)) {
      $columns = array_keys($data_array[0]);

      fputcsv($file_handle, $columns);

      foreach ($data_array as $row) {
        fputcsv($file_handle, $row);
      }
    }

    fclose($file_handle);

    $headers = array(
      'Content-Type' => 'text/csv',
    );

    return response()->download($file_path, 'output.csv', $headers);
  }
}
