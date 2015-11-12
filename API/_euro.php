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


class Euro
	{
	function __construct()
	    {
	    // Check if BTC-e data is up to date.
	    if(file_exists("data/btce_eur.dat"))
		{
		//check age
		if(filemtime("data/btce_eur.dat") < time() - 600)
		    {
		    // File older than 10 minutes.
		    $this->fetch_btce();
		    }
		}
		else
		{
		$this->fetch_btce();
		}
	    // Check if Kraken data is up to date.
	    if(file_exists("data/kraken_eur.dat"))
		{
		//check age
		if(filemtime("data/kraken_eur.dat") < time() - 600)
		    {
		    // File older than 10 minutes.
		    $this->fetch_kraken();
		    }
		}
		else
		{
		$this->fetch_kraken();
		}
	    // Check if clevercoin data is up to date.
	    if(file_exists("data/clevercoin_eur.dat"))
		{
		//check age
		if(filemtime("data/clevercoin_eur.dat") < time() - 600)
		    {
		    // File older than 10 minutes.
		    $this->fetch_clevercoin();
		    }
		}
		else
		{
		$this->fetch_clevercoin();
		}
	    // Check if bl3p data is up to date.
	    if(file_exists("data/bl3p_eur.dat"))
		{
		//check age
		if(filemtime("data/bl3p_eur.dat") < time() - 600)
		    {
		    // File older than 10 minutes.
		    $this->fetch_bl3p();
		    }
		}
		else
		{
		$this->fetch_bl3p();
		}
	    }
	
	function fetch_btce()
	    {
	    // Get data from BTC-e and store in data/btce_eur.dat
	    $ch = curl_init();
	
	    curl_setopt($ch, CURLOPT_URL, "https://btc-e.com/api/3/ticker/btc_eur");
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    $output = curl_exec($ch);
	    curl_close($ch);
	    $data = json_decode($output, true);
	    $volume = $data['btc_eur']['vol_cur'];
	    $price = round(($data['btc_eur']['buy'] + $data['btc_eur']['sell'])/2, 8);
	
	    $save_data = array("volume" => $volume, "price" => $price);
	    $output = json_encode($save_data);
	
	    file_put_contents("data/btce_eur.dat", $output, LOCK_EX);
	    }
	
	function fetch_kraken()
	    {
	    // Get data from kraken and store in data/kraken_eur.dat
	    $ch = curl_init();
	
	    curl_setopt($ch, CURLOPT_URL, "https://api.kraken.com/0/public/Ticker?pair=XBTEUR");
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    $output = curl_exec($ch);
	    curl_close($ch);
	    $data = json_decode($output, true);
	    $volume = $data['result']['XXBTZEUR']['v'][1];
	    $price = round(($data['result']['XXBTZEUR']['b'][0] + $data['result']['XXBTZEUR']['a'][0])/2, 8);
	
	    $save_data = array("volume" => $volume, "price" => $price);
	    $output = json_encode($save_data);
	
	    file_put_contents("data/kraken_eur.dat", $output, LOCK_EX);
	    }
	
	function fetch_clevercoin()
	    {
	    // Get data from clevercoin and store in data/clevercoin_eur.dat
	    $ch = curl_init();
	
	    curl_setopt($ch, CURLOPT_URL, "https://api.clevercoin.com/v1/ticker");
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    $output = curl_exec($ch);
	    curl_close($ch);
	    $data = json_decode($output, true);
	    $volume = $data['volume'];
	    $price = round(($data['ask'] + $data['bid'])/2, 8);
	
	    $save_data = array("volume" => $volume, "price" => $price);
	    $output = json_encode($save_data);
	
	    file_put_contents("data/clevercoin_eur.dat", $output, LOCK_EX);
	    }
	
	function fetch_bl3p()
	    {
	    // Get data from bl3p and store in data/bl3p_eur.dat
	    $ch = curl_init();
	
	    curl_setopt($ch, CURLOPT_URL, "https://api.bl3p.eu/1/BTCEUR/ticker");
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    $output = curl_exec($ch);
	    curl_close($ch);
	    $data = json_decode($output, true);
	    $volume = $data['volume']['24h'];
	    $price = round(($data['ask'] + $data['bid'])/2, 8);
	
	    $save_data = array("volume" => $volume, "price" => $price);
	    $output = json_encode($save_data);
	
	    file_put_contents("data/bl3p_eur.dat", $output, LOCK_EX);
	    }
	
	function get_price()
	    {
	    // open files and get contents in array
	    $btc_eur = json_decode(file_get_contents("data/btce_eur.dat"), true);
	    $kraken_eur = json_decode(file_get_contents("data/kraken_eur.dat"), true);
	    $clevercoin_eur = json_decode(file_get_contents("data/clevercoin_eur.dat"), true);
	    $bl3p_eur = json_decode(file_get_contents("data/bl3p_eur.dat"), true);
	
	    $trade_totals = $btc_eur["volume"] + $kraken_eur["volume"] + $clevercoin_eur["volume"] + $bl3p_eur["volume"];
	
	    $price = ($btc_eur["price"]*($btc_eur["volume"]/$trade_totals)) + ($kraken_eur["price"]*($kraken_eur["volume"]/$trade_totals)) +
	             ($clevercoin_eur["price"]*($clevercoin_eur["volume"]/$trade_totals)) + ($bl3p_eur["price"]*($bl3p_eur["volume"]/$trade_totals));
	    return($price);
	    }
	}

?>