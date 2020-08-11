<nav class="navbar navbar-expand-sm bg-dark navbar-dark">
	<div class="container-fluid">
		<div class="navbar-header">
			<a class="navbar-brand" href="pokemon.php">CS348 Project</a>
		</div>
		<ul class="nav navbar-nav navbar-right">
			<li class="nav-item"><a class="nav-link" href="pokemon.php"><span class="fa fa-search"> Search Pokemon </a></li>
			<li class="nav-item"><a class="nav-link" href="catchPokemon.php"><span class="fa fa-compass"> Catch Pokemon </a></li>
			<li class="nav-item"><a class="nav-link" href="organizePokemon.php"><span class="fa fa-sitemap"> Organize Pokemon </a></li>
			<li class="nav-item"><a class="nav-link" href="partyshare.php"><span class="fa fa-share-alt"> Parties </a></li>
			<li class="nav-item"><a class="nav-link" href="battle.php"><span class="fa fa-trophy"> Battle </a></li>
			<li class="nav-item"><a class="nav-link" href="profile.php"><span class="fa fa-user"></span> <?php echo $_SESSION['name'];?></a></li>
			<li class="nav-item"><a class="nav-link" href="index.php"><span class="fa fa-sign-out"></span> Logout</a></li>
		</ul>
	</div>
</nav>
