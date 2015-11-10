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


class Bitcoin
	{
	function __construct()
	    {
	    // Check if Cryptsy data is up to date.
	    if(file_exists("data/cryptsy.dat"))
		{
		//check age
		if(filemtime("data/cryptsy.dat") < time() - 600)
		    {
		    // File older than 10 minutes.
		    $this->fetch_cryptsy();
		    }
		}
		else
		{
		$this->fetch_cryptsy();
		}
	
	    if(file_exists("data/bter.dat"))
		{
		//check age
		if(filemtime("data/bter.dat") < time() - 600)
		    {
		    // File older than 10 minutes.
		    $this->fetch_bter();
		    }
		}
		else
		{
		$this->fetch_bter();
		}
	
	    if(file_exists("data/cexio.dat"))
		{
		//check age
		if(filemtime("data/cexio.dat") < time() - 600)
		    {
		    // File older than 10 minutes.
		    $this->fetch_cexio();
		    }
		}
		else
		{
		$this->fetch_cexio();
		}
	    }
	
	function fetch_cryptsy()
	    {
	    // Get data from Cryptsy and store in data/cryptsy.dat
	    $ch = curl_init();
	
	    curl_setopt($ch, CURLOPT_URL, "https://api.cryptsy.com/api/v2/markets/160/");
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    $output = curl_exec($ch);
	    $data = json_decode($output, true);
	    $volume = $data['data']['24hr']['volume'];
	
	    curl_setopt($ch, CURLOPT_URL, "https://api.cryptsy.com/api/v2/markets/160/ticker");
	    $output = curl_exec($ch);
	    curl_close($ch);
	    $data = json_decode($output, true);
	    $price = round(($data['data']['ask'] + $data['data']['bid'])/2, 8);
	
	    $save_data = array("volume" => $volume, "price" => $price);
	    $output = json_encode($save_data);
	
	    file_put_contents("data/cryptsy.dat", $output, LOCK_EX);
	    }
	
	function fetch_bter()
	    {
	    // Get data from Bter and store in data/bter.dat
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, "http://data.bter.com/api/1/ticker/aur_btc");
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    $output = curl_exec($ch);
	    $data = json_decode($output, true);
	    $volume = $data['vol_aur'];
	    $price = round(($data['sell'] + $data['buy'])/2, 8);
	
	    $save_data = array("volume" => $volume, "price" => $price);
	    $output = json_encode($save_data);
	
	    file_put_contents("data/bter.dat", $output, LOCK_EX);
	    }
	
	function fetch_cexio()
	    {
	    // Get data from CEX.io and store in data/cexio.dat
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, "https://cex.io/api/ticker/AUR/BTC");
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    $output = curl_exec($ch);
	    $data = json_decode($output, true);
	    $volume = $data['volume'];
	    $price = round(($data['ask'] + $data['bid'])/2, 8);
	
	    $save_data = array("volume" => $volume, "price" => $price);
	    $output = json_encode($save_data);
	
	    file_put_contents("data/cexio.dat", $output, LOCK_EX);
	    }
	
	function get_price()
	    {
	    // open files and get contents in array
	    $cryptsy = json_decode(file_get_contents("data/cryptsy.dat"), true);
	    $bter = json_decode(file_get_contents("data/bter.dat"), true);
	    $cexio = json_decode(file_get_contents("data/cexio.dat"), true);
	    
	    $trade_totals = $cryptsy["volume"] + $bter["volume"] + $cexio["volume"];
	    
	    $price = ($cryptsy["price"]*($cryptsy["volume"]/$trade_totals)) + ($bter["price"]*($bter["volume"]/$trade_totals)) + ($cexio["price"]*($cexio["volume"]/$trade_totals));
	    
	    return($price);
	    }
	
	}

?>