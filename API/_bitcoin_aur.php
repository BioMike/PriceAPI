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
	    }
	    if(file_exists("data/cryptopia.dat"))
		{
		//check age
		if(filemtime("data/cryptopia.dat") < time() - 600)
		    {
		    // File older than 10 minutes.
		    $this->fetch_cryptopia();
		    }
		}
		else
		{
		$this->fetch_cryptopia();
		}
	    if(file_exists("data/bittrex_aur.dat"))
		{
		//check age
		if(filemtime("data/bittrex_au.dat") < time() - 600)
		    {
		    // File older than 10 minutes.
		    $this->fetch_bittrex();
		    }
		}
		else
		{
		$this->fetch_bittrex();
		}
	    }
	
	function fetch_bittrex()
	    {
	    // Get data from Bittrex and store in data/bittrex.dat
	    $ch = curl_init();

	    curl_setopt($ch, CURLOPT_URL, "https://bittrex.com/api/v1.1/public/getmarketsummary?market=BTC-AUR");
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    $output = curl_exec($ch);
	    $data = json_decode($output, true);
	    curl_close($ch);
	    $volume = $data['result'][0]['Volume'];
	    $price = round(($data['result'][0]['Ask'] + $data['result'][0]['Bid'])/2, 8);

	    $save_data = array("volume" => $volume, "price" => $price);
	    $output = json_encode($save_data);
	
	    file_put_contents("data/bittrex_aur.dat", $output, LOCK_EX);
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
	
	function fetch_cryptopia()
	    {
	    // Get data from Bter and store in data/bter.dat
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, "https://www.cryptopia.co.nz/api/GetMarket/2671");
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    $output = curl_exec($ch);
	    $data = json_decode($output, true);
	    $volume = $data['Volume'];
	    $price = round(($data['AskPrice'] + $data['BidPrice'])/2, 8);
	
	    $save_data = array("volume" => $volume, "price" => $price);
	    $output = json_encode($save_data);
	
	    file_put_contents("data/cryptopia.dat", $output, LOCK_EX);
	    }
	
	function get_price()
	    {
	    // open files and get contents in array
	    $cryptsy = json_decode(file_get_contents("data/cryptsy.dat"), true);
	    $bter = json_decode(file_get_contents("data/bter.dat"), true);
	    $cryptopia = json_decode(file_get_contents("data/cryptopia.dat"), true);
	    $bittrex = json_decode(file_get_contents("data/bittrex_aur.dat"), true);
	    
	    $trade_totals = $cryptsy["volume"] + $bter["volume"] + $cryptopia["volume"] + $bittrex["volume"];
	    
	    $price = ($cryptsy["price"]*($cryptsy["volume"]/$trade_totals)) + ($bter["price"]*($bter["volume"]/$trade_totals)) + 
	             ($cryptopia["price"]*($cryptopia["volume"]/$trade_totals)) + ($bittrex["price"]*($bittrex["volume"]/$trade_totals));
	    
	    return($price);
	    }
	
	}

?>