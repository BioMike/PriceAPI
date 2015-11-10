<?php
//    <one line to give the program's name and a brief idea of what it does.>
//    Copyright (C) 2015  Myckel Habets
//
//    This program is free software: you can redistribute it and/or modify
//    it under the terms of the GNU Affero General Public License as published
//    by the Free Software Foundation, either version 3 of the License, or
//    (at your option) any later version.
//
//    This program is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU Affero General Public License for more details.
//
//    You should have received a copy of the GNU Affero General Public License
//    along with this program.  If not, see <http://www.gnu.org/licenses/>.

include("_bitcoin.php");
include("_euro.php");
include("_usd.php");
include("_ISK.php");

// [BTC|ISK|EUR|USD]
if(array_key_exists("cur", $_GET))
    {
    $currency = strtoupper($_GET['cur']);
    }
    else
    {
    header("location: http://ercinee.eu/");
    //echo "No currency input: use /API/index.php?cur=[BTC|ISK|EUR|USD]";
    //die();
    }

$btc_class = new Bitcoin();

switch($currency)
    {
    case "BTC":
	$price = $btc_class->get_price();
	printf("%.8f", $price);
	break;
    case "ISK":
	$eur_class = new Euro();
	$isk_class = new Krona();
	$btc_eur = $eur_class->get_price();
	$eur_isk = $isk_class->get_price();
	$price = ($btc_class->get_price() * $btc_eur * $eur_isk);
	printf("%.5f", $price);
	break;
    case "EUR":
	$eur_class = new Euro();
	$btc_eur = $eur_class->get_price();
	$price = ($btc_class->get_price() * $btc_eur);
	printf("%.5f", $price);
	break;
    case "USD":
	$usd_class = new USD();
	$btc_usd = $usd_class->get_price();
	$price = ($btc_class->get_price() * $btc_usd);
	printf("%.5f", $price);
	break;
    default:
	header("location: http://ercinee.eu/");
	//echo "No currency input: use /API/index.php?cur=[BTC|ISK|EUR|USD]";
	//die();
    }

?>