<?php include('lib/header.php'); ?>

<div class="container">
	<div class="row">
		<div class="col-lg-10">
			<h2>Basic Search<small> Searches All Available Fields</small></h2><p><a href="/advanced.php" >Switch To Advanced Search</a></p>
		</div>
	</div>
	<div class="row">
    <div class="col-lg-10">
     	<form action="/search.php" method="post" role="search">
      		<input type="text" class="form-control input-lg" placeholder="ex: 8080 http-method" name="query" id="query">
    </div>

    <div class="col-lg-2">
    	  <input type="submit" class="btn btn-default btn-lg btn-success">
     </div>
 </div>

    </form>
</div>


<?php include('lib/footer.php'); ?>