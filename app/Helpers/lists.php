<?php
function lists_login_status()
{
    return [
        1 => 'Active',
        0 => 'Inactive',
    ];
}

function lists_customer()
{
    $arr = \App\Models\Customer::pluck('name', 'id');
    return $arr->toArray();
}



function lists_sales()
{
    $arr = \App\Models\Sales::pluck('title', 'id');
    return $arr->toArray();
}
