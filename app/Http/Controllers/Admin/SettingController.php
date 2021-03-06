<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\JwtKeys;
use App\Http\Controllers\Controller;
use App\Setting;
use Illuminate\Http\Request;

/**
 * CRUD Controller for Setting entity.
 *
 * @package App\Http\Controllers\Admin
 */
class SettingController extends Controller
{

    public function __construct()
    {
        $this->middleware( 'admin' );
    }

    public function edit()
    {
        $settings = Setting::all()->keyBy( 'key' );

        return view( 'admin.settings.edit' )
            ->withSettings( $settings );
    }

    public function update( Request $request )
    {

        $settings = $request->all();

        foreach ( $settings as $k => $v )
        {
            if ( 'settings_' == substr( $k, 0, 9 ) )
            {
                $key = substr( $k, 9 );
                $setting = Setting::find( $key );
                if ( ! $setting )
                {
                    // TODO Setting not found
                }
                else if ( $setting->value != $v )
                {
                    $setting->value = $v;
                    $setting->save();
                }
            }
        }

        return redirect()->action( 'Admin\SettingController@edit' );

    }

    public function keys()
    {
        return view( 'admin.settings.keys' );
    }

    public function keys_generate( Request $request )
    {
        $keysHelper = new JwtKeys();

        // Generate keys
        $keys = $keysHelper->makeKeys();

        if ( ! $keys )
        {
            redirect()->action( 'Admin\SettingController@edit' );
        }

        // Save new keys to DB
        $keysHelper->setKeys( $keys );

        // Redirect to settings
        return redirect()->action( 'Admin\SettingController@edit' );
    }

}
