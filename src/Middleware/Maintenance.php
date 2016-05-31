<?php namespace Sanatorium\Maintenance\Middleware;

use Closure;
use Session;
use Sentinel;
use Theme;

class Maintenance
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        Theme::setActive( config('platform-themes.active.frontend') );

        if ( config('sanatorium-maintenance.mode') == 1 ) {

            $allowed_raw = explode(',', config('sanatorium-maintenance.allowed'));
            $allowed = [];

            foreach($allowed_raw as $raw) {

                $allowed[] = trim($raw);

            }

            if ( !in_array($_SERVER['REMOTE_ADDR'], $allowed) ) {

                if ( Sentinel::check() ) {
                    
                    if ( !Sentinel::inRole('admin') || config('sanatorium-maintenance.admin_allowed') == false ) {
                        
                        echo view('sanatorium/maintenance::maintenance');
                        dd();
                    
                    }
                
                } else {

                    echo view('sanatorium/maintenance::maintenance');
                    dd();
                
                }
                
            }

        }

        return $next($request);
    }
}
