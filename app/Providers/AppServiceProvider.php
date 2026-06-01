<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
       View::composer('layouts.backendapp', function ($view) {

        $reorderItems = DB::table('approve_stocks')
            ->join('items', 'approve_stocks.item_id', '=', 'items.id')

            ->select(
                'items.id',
                'items.name',
                'items.reorder_level',
                DB::raw('SUM(approve_stocks.qty) as total_qty')
            )

            ->where('approve_stocks.status', 'approved')

            ->groupBy(
                'items.id',
                'items.name',
                'items.reorder_level'
            )

            ->havingRaw('SUM(approve_stocks.qty) <= items.reorder_level')

            ->get();

              
        // Count the items
        $reorderItemsCount = $reorderItems->count();

        $view->with([
            'reorderItems' => $reorderItems,
            'reorderItemsCount' => $reorderItemsCount
          
        ]);

     

    });

     View::composer('dashboard', function ($view) {
    $notifications = DB::table('approve_stocks')
    ->join('items', 'approve_stocks.item_id', '=', 'items.id')
    ->whereDate('approve_stocks.expiry_date', '<=', now()->addMonths(3))
    ->whereDate('approve_stocks.expiry_date', '>=', now())
    ->select(
        'items.name',
        'approve_stocks.qty',
        'approve_stocks.expiry_date',
         DB::raw('DATEDIFF(approve_stocks.expiry_date, CURDATE()) as days_left')
    )
    ->orderBy('approve_stocks.expiry_date', 'asc')
    
    ->get();

    $reorderItems = DB::table('approve_stocks')
            ->join('items', 'approve_stocks.item_id', '=', 'items.id')

            ->select(
                'items.id',
                'items.name',
                'items.reorder_level',
                DB::raw('SUM(approve_stocks.qty) as total_qty')
            )

            ->where('approve_stocks.status', 'approved')

            ->groupBy(
                'items.id',
                'items.name',
                'items.reorder_level'
            )

            ->havingRaw('SUM(approve_stocks.qty) <= items.reorder_level')

            ->get();
 

        // Count the items
        $reorderItemsCount = $reorderItems->count();

        // Count the items
      $count = DB::table('approve_stocks')
    ->whereDate('expiry_date', '<=', now()->addMonths(3))
    ->whereDate('expiry_date', '>=', now())
    ->count();
        $view->with([
            
            'count' => $count,
            'notifications' =>$notifications,
            'reorderItemsCount' =>$reorderItemsCount,
            'reorderItems' =>$reorderItems
        ]);


     });
    }
}
