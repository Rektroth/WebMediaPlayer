<?php
	require_once "authcookiesessionvalidate.php";
	
	$RATING = array(
		"NR",
		"G",
		"PG",
		"PG-13",
		"R",
		"NC-17",
		"TV-Y",
		"TV-Y7",
		"TV-G",
		"TV-PG",
		"TV-14",
		"TV-MA"
	);
	
	$GENRE = array(
		"Action",
		"Adult",
		"Adventure",
		"Animation",
		"Biography",
		"Comedy",
		"Crime",
		"Documentary",
		"Drama",
		"Family",
		"Fantasy",
		"Film Noir",
		"Game-Show",
		"History",
		"Horror",
		"Musical",
		"Music",
		"Mystery",
		"News",
		"Reality-TV",
		"Romance",
		"Sci-Fi",
		"Short",
		"Sport",
		"Talk-Show",
		"Thriller",
		"War",
		"Western"
	);
	
	$MOVIE = "movie";
	$SHOW = "show";
	$ALBUM = "album";
	$BOOK = "book";
	
	$MAX_FILE_SIZE = 1000000;
?>