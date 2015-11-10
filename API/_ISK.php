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




class Krona
	{
	function __construct()
	    {
	    // Check if BTC-e data is up to date.
	    if(file_exists("data/eur_isk.dat"))
		{
		//check age 12 hours
		if(filemtime("data/eur_isk.dat") < time() - 43200)
		    {
		    // File older than 12 hours.
		    $this->fetch_isk();
		    }
		}
		else
		{
		$this->fetch_isk();
		}
	    }
	
	function fetch_isk()
	    {
	    // Get data from Yahoo and store in data/eur_isk.dat
	    $ch = curl_init();
	
	    curl_setopt($ch, CURLOPT_URL, "http://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20yahoo.finance.xchange%20where%20pair%20in%20(%22EURISK%22)&format=json&env=store://datatables.org/alltableswithkeys&callback=");
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    $output = curl_exec($ch);
	    curl_close($ch);
	    $data = json_decode($output, true);
	    $volume = 100;
	    $price = round(($data['query']['results']['rate']['Ask'] + $data['query']['results']['rate']['Bid'])/2, 8);
	
	    $save_data = array("volume" => $volume, "price" => $price);
	    $output = json_encode($save_data);
	
	    file_put_contents("data/eur_isk.dat", $output, LOCK_EX);
	    }
	
	function get_price()
	    {
	    // open files and get contents in array
	    $eur_isk = json_decode(file_get_contents("data/eur_isk.dat"), true);
	    //$kraken_eur = json_decode(file_get_contents("data/kraken_eur.dat"), true);
	
	    //$trade_totals = $btc_eur["volume"] + $kraken_eur["volume"];
	
	    //$price = ($btc_eur["price"]*($btc_eur["volume"]/$trade_totals)) + ($kraken_eur["price"]*($kraken_eur["volume"]/$trade_totals));
	    return($eur_isk["price"]);
	    }
	
	}

?>