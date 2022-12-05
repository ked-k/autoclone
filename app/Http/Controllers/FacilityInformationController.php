<?php

namespace App\Http\Controllers;

use App\Models\FacilityInformation;
use Illuminate\Http\Request;

class FacilityInformationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $profile = FacilityInformation::first();

        return view('super-admin.manageFacilityProfile', compact('profile'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request);
        $request->validate([
            'facility_name' => ['required', 'string', 'max:255'],
            'facility_type' => ['required', 'string', 'max:255'],
            'physical_address' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'contact' => ['required', 'string', 'max:20'],
            'logo' => ['required', 'image', 'mimes:jpg,png', 'max:300'],
        ]);
        //    dd($request);
        $logoPath = '';
        $logo2Path = '';

        if ($request->hasFile('logo') && $request->hasFile('logo2')) {
            $request->validate(['logo' => 'image|max:300|dimensions:max_width=600,max_height=400',
                'logo2' => 'image|max:300|dimensions:max_width=600,max_height=400', ]);

            $logoName = 'logo.'.$request->file('logo')->extension();
            $logoPath = $request->file('logo')->storeAs('facilitylogo', $logoName, 'public');

            $logo2Name = 'logo2.'.$request->file('logo2')->extension();
            $logo2Path = $request->file('logo2')->storeAs('facilitylogo', $logo2Name, 'public');
        } elseif ($request->hasFile('logo')) {
            $request->validate(['logo' => 'image|max:300|dimensions:max_width=600,max_height=400']);

            $logoName = 'logo.'.$request->file('logo')->extension();
            $logoPath = $request->file('logo')->storeAs('facilitylogo', $logoName, 'public');
            $logo2Path = null;
        } else {
            $logoPath = null;
            $logo2Path = null;
        }

        $facilityInfo = new FacilityInformation();

        $facilityInfo->facility_name = $request->facility_name;
        $facilityInfo->slogan = $request->slogan;
        $facilityInfo->about = $request->about;
        $facilityInfo->facility_type = $request->facility_type;
        $facilityInfo->physical_address = $request->physical_address;
        $facilityInfo->address2 = $request->address2;
        $facilityInfo->contact = $request->contact;
        $facilityInfo->contact2 = $request->contact2;
        $facilityInfo->email = $request->email;
        $facilityInfo->email2 = $request->email2;
        $facilityInfo->fax = $request->fax;
        $facilityInfo->website = $request->website;
        $facilityInfo->tin = $request->tin;
        $facilityInfo->logo = $logoPath;
        $facilityInfo->logo2 = $logo2Path;
        $facilityInfo->save();

        return redirect()->back()->with('success', 'Facility Information Recorded Successfully');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, FacilityInformation $facilityInformation)
    {
        $request->validate([

            'facility_name' => ['required', 'string', 'max:255'],
            'facility_type' => ['required', 'string', 'max:255'],
            'physical_address' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'contact' => ['required', 'string', 'max:20'],
            // 'logo' => ['required','image', 'mimes:jpg,png', 'max:300'],
        ]);

        $currentLogo = $facilityInformation->logo;
        $currentLogo2 = $facilityInformation->logo2;

        $logoPath = '';
        $logo2Path = '';

        if ($request->hasFile('logo') && $request->hasFile('logo2')) {
            $request->validate(['logo' => 'image|max:300|dimensions:max_width=600,max_height=400',
                'logo2' => 'image|max:300|dimensions:max_width=600,max_height=400', ]);

            $logoName = 'logo.'.$request->file('logo')->extension();
            $logoPath = $request->file('logo')->storeAs('facilitylogo', $logoName, 'public');

            $logo2Name = 'logo2.'.$request->file('logo2')->extension();
            $logo2Path = $request->file('logo2')->storeAs('facilitylogo', $logo2Name, 'public');

        // $facilityLogo = storage_path('app/public/').$currentLogo;

        // if (file_exists($facilityLogo) && file_exists($facilityLogo2)) {
            //     @unlink($facilityLogo);
            //     @unlink($facilityLogo2);
        // }
        } elseif ($request->hasFile('logo')) {
            $request->validate(['logo' => 'image|max:300|dimensions:max_width=600,max_height=400']);

            $logoName = 'logo.'.$request->file('logo')->extension();
            $logoPath = $request->file('logo')->storeAs('facilitylogo', $logoName, 'public');
            $logo2Path = $currentLogo2;
        } elseif ($request->hasFile('logo2')) {
            $request->validate(['logo2' => 'image|max:300|dimensions:max_width=600,max_height=400']);

            $logo2Name = 'logo2.'.$request->file('logo2')->extension();
            $logo2Path = $request->file('logo2')->storeAs('facilitylogo', $logo2Name, 'public');
            $logoPath = $currentLogo;
        } else {
            $logoPath = $currentLogo;
            $logo2Path = $currentLogo2;
        }

        $facilityInformation->facility_name = $request->facility_name;
        $facilityInformation->slogan = $request->slogan;
        $facilityInformation->about = $request->about;
        $facilityInformation->facility_type = $request->facility_type;
        $facilityInformation->physical_address = $request->physical_address;
        $facilityInformation->address2 = $request->address2;
        $facilityInformation->contact = $request->contact;
        $facilityInformation->contact2 = $request->contact2;
        $facilityInformation->email = $request->email;
        $facilityInformation->email2 = $request->email2;
        $facilityInformation->fax = $request->fax;
        $facilityInformation->website = $request->website;
        $facilityInformation->tin = $request->tin;
        $facilityInformation->logo = $logoPath;
        $facilityInformation->logo2 = $logo2Path;

        $facilityInformation->update();

        return redirect()->back()->with('success', ' Facility Profile Updated successfully');
    }
}
