<?php include('lib/header.php'); ?>

<div class="container">
	<div class="row">
		<div class="col-lg-10">
			<h2>Advanced Search<small> Specify Specific Fields To Search, Ommiting The Others</small></h2><p><a href="/" >Switch To Basic Search</a></p>
		</div>
	</div>
	<div class="row">
    <div class="col-lg-10">
     	<form  action="/search.php" method="post" role="search">
      		<input type="text" class="form-control input-lg" placeholder="ex: 8080 http-method" name="query" id="query">
    </div>

    <div class="col-lg-2">    
    	 <input type="submit" class="btn btn-default btn-lg btn-success">
     </div>
 </div>
    
    <div class="container">
     <div class="row"/>
    	<input type="checkbox" name="service_banner" value="service_banner" name="service_banner" id="service_banner"> Banner<br>
     	<input type="checkbox" name="host_country" value="host_country" name="host_country" id="host_country"> Country<br>
     	<input type="checkbox" name="host_gelocation" value="host_geolocation" name="host_geolocation" id="host_geolocation"> Geo-Location<br>
		<input type="checkbox" name="host_ip" value="host_ip" name="host_ip" id="host_ip"> Ip<br>
		<input type="checkbox" name="service_port" value="service_port" name="service_port" id="service_port"> Port<br>
		<input type="checkbox" name="service_protocol" value="service_protocol" name="service_protocol" id="service_protocol"> Protocol<br>
		<input type="checkbox" name="rapid7_hash" value="rapid7_hash" name="rapid7_hash" id="rapid7_hash"> Rapid7 Hash<br>
		<input type="checkbox" name="service_type" value="service_type" name="service_type" id="service_type"> Service Type<br>
	</div>
</div>

    </form>
</div>


<?php include('lib/footer.php'); ?>