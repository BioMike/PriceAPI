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


class USD
	{
	function __construct()
	    {
	    // Check if BTC-e data is up to date.
	    if(file_exists("data/btce_usd.dat"))
		{
		//check age
		if(filemtime("data/btce_usd.dat") < time() - 600)
		    {
		    // File older than 10 minutes.
		    $this->fetch_btce();
		    }
		}
		else
		{
		$this->fetch_btce();
		}
	    // Check if Bitstamp data is up to date.
	    if(file_exists("data/bitstamp_usd.dat"))
		{
		//check age
		if(filemtime("data/bitstamp_usd.dat") < time() - 600)
		    {
		    // File older than 10 minutes.
		    $this->fetch_bitstamp();
		    }
		}
		else
		{
		$this->fetch_bitstamp();
		}
	    // Check if Bitfinex data is up to date.
	    if(file_exists("data/bitfinex_usd.dat"))
		{
		//check age
		if(filemtime("data/bitfinex_usd.dat") < time() - 600)
		    {
		    // File older than 10 minutes.
		    $this->fetch_bitfinex();
		    }
		}
		else
		{
		$this->fetch_bitfinex();
		}
	    }
	
	function fetch_btce()
	    {
	    // Get data from BTC-e and store in data/btce_usd.dat
	    $ch = curl_init();
	
	    curl_setopt($ch, CURLOPT_URL, "https://btc-e.com/api/3/ticker/btc_usd");
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    $output = curl_exec($ch);
	    curl_close($ch);
	    $data = json_decode($output, true);
	    $volume = $data['btc_usd']['vol_cur'];
	    $price = round(($data['btc_usd']['buy'] + $data['btc_usd']['sell'])/2, 8);
	
	    $save_data = array("volume" => $volume, "price" => $price);
	    $output = json_encode($save_data);
	
	    file_put_contents("data/btce_usd.dat", $output, LOCK_EX);
	    }
	
	function fetch_bitstamp()
	    {
	    // Get data from Bitstamp and store in data/bitstamp_usd.dat
	    $ch = curl_init();
	
	    curl_setopt($ch, CURLOPT_URL, "https://www.bitstamp.net/api/ticker/");
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    $output = curl_exec($ch);
	    curl_close($ch);
	    $data = json_decode($output, true);
	    $volume = $data['volume'];
	    $price = round(($data['bid'] + $data['ask'])/2, 8);
	
	    $save_data = array("volume" => $volume, "price" => $price);
	    $output = json_encode($save_data);
	
	    file_put_contents("data/bitstamp_usd.dat", $output, LOCK_EX);
	    }
	
	function fetch_bitfinex()
	    {
	    // Get data from Bitfinex and store in data/bitfinex_usd.dat
	    $ch = curl_init();
	
	    curl_setopt($ch, CURLOPT_URL, "https://api.bitfinex.com/v1/pubticker/BTCUSD");
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    $output = curl_exec($ch);
	    curl_close($ch);
	    $data = json_decode($output, true);
	    $volume = $data['volume'];
	    $price = round(($data['bid'] + $data['ask'])/2, 8);
	
	    $save_data = array("volume" => $volume, "price" => $price);
	    $output = json_encode($save_data);
	
	    file_put_contents("data/bitfinex_usd.dat", $output, LOCK_EX);
	    }
	
	function get_price()
	    {
	    // open files and get contents in array
	    $btce = json_decode(file_get_contents("data/btce_usd.dat"), true);
	    $bitstamp = json_decode(file_get_contents("data/bitstamp_usd.dat"), true);
	    $bitfinex = json_decode(file_get_contents("data/bitfinex_usd.dat"), true);
	
	    $trade_totals = $btce["volume"] + $bitstamp["volume"] + $bitfinex["volume"];
	
	    $price = ($btce["price"]*($btce["volume"]/$trade_totals)) + ($bitstamp["price"]*($bitstamp["volume"]/$trade_totals)) + ($bitfinex["price"]*($bitfinex["volume"]/$trade_totals));
	    return($price);
	    }
	
	}

?>