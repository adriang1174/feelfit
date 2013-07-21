<?php
//BindEvents Method @1-ED9E9B25
function BindEvents()
{
    global $movimientofacturacion;
    $movimientofacturacion->Navigator->CCSEvents["BeforeShow"] = "movimientofacturacion_Navigator_BeforeShow";
}
//End BindEvents Method

//movimientofacturacion_Navigator_BeforeShow @18-4D797FE4
function movimientofacturacion_Navigator_BeforeShow(& $sender)
{
    $movimientofacturacion_Navigator_BeforeShow = true;
    $Component = & $sender;
    $Container = & CCGetParentContainer($sender);
    global $movimientofacturacion; //Compatibility
//End movimientofacturacion_Navigator_BeforeShow

//Close movimientofacturacion_Navigator_BeforeShow @18-1E9BA27C
    return $movimientofacturacion_Navigator_BeforeShow;
}
//End Close movimientofacturacion_Navigator_BeforeShow


?>
