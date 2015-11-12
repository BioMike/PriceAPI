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
	    if(file_exists("data/bittrex.dat"))
		{
		//check age
		if(filemtime("data/bittrex.dat") < time() - 600)
		    {
		    // File older than 10 minutes.
		    $this->fetch_bittrex();
		    }
		}
		else
		{
		$this->fetch_bittrex();
		}
	
	    if(file_exists("data/bleutrade.dat"))
		{
		//check age
		if(filemtime("data/bleutrade.dat") < time() - 600)
		    {
		    // File older than 10 minutes.
		    $this->fetch_bleutrade();
		    }
		}
		else
		{
		$this->fetch_bleutrade();
		}
	    }
	
	function fetch_bittrex()
	    {
	    // Get data from Bittrex and store in data/bittrex.dat
	    $ch = curl_init();
	
	    curl_setopt($ch, CURLOPT_URL, "https://bittrex.com/api/v1.1/public/getmarketsummary?market=BTC-NLG");
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    $output = curl_exec($ch);
	    $data = json_decode($output, true);
	    curl_close($ch);
	    $volume = $data['result'][0]['Volume'];
	    $price = round(($data['result'][0]['Ask'] + $data['result'][0]['Bid'])/2, 8);
	
	    $save_data = array("volume" => $volume, "price" => $price);
	    $output = json_encode($save_data);
	
	    file_put_contents("data/bittrex.dat", $output, LOCK_EX);
	    }
	
	function fetch_bleutrade()
	    {
	    // Get data from Bleutrade and store in data/bleutrade.dat
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, "https://bleutrade.com/api/v2/public/getmarketsummary?market=NLG_BTC");
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    $output = curl_exec($ch);
	    $data = json_decode($output, true);
	    $volume = $data['result'][0]['Volume'];
	    $price = round(($data['result'][0]['Bid'] + $data['result'][0]['ask'])/2, 8);
	
	    $save_data = array("volume" => $volume, "price" => $price);
	    $output = json_encode($save_data);
	
	    file_put_contents("data/bleutrade.dat", $output, LOCK_EX);
	    }
	
	function get_price()
	    {
	    // open files and get contents in array
	    $bittrex = json_decode(file_get_contents("data/bittrex.dat"), true);
	    $bleutrade = json_decode(file_get_contents("data/bleutrade.dat"), true);
	    
	    $trade_totals = $bittrex["volume"] + $bleutrade["volume"];
	    
	    $price = ($bittrex["price"]*($bittrex["volume"]/$trade_totals)) + ($bleutrade["price"]*($bleutrade["volume"]/$trade_totals));
	    
	    return($price);
	    }
	
	}

?>