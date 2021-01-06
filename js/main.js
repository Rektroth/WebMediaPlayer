$(window).ready(updateHeight);
$(window).resize(updateHeight);

$(document).ready(function()
{
	var episodeLists = Array.prototype.slice.call(document.getElementsByClassName("season-list"));
	var episodeListsLength = episodeLists.length;
	
	for (var i = episodeListsLength - 1; i > 0; i--)
	{
		document.getElementsByClassName("season-list")[i].parentNode.removeChild(document.getElementsByClassName("season-list")[i]);
	}
	
	$("#searchInput").keyup(function(event)
	{
		event.preventDefault();
		
		if (event.key !== "Enter")
		{
			return;
		}
		
		window.open("search.php?s=" + document.getElementById("searchInput").value, "_self");
	});
	
	$("#categories-button").click(function(event)
	{
		event.preventDefault();
		
		if (document.getElementsByClassName("categories-panel")[0].style.display == "block")
		{
			document.getElementsByClassName("categories-panel")[0].style.display = "none";
		}
		else
		{
			document.getElementsByClassName("categories-panel")[0].style.display = "block";
		}
	});
	
	$("#add-genre-button").click(function(event)
	{
		event.preventDefault();
		
		const MAX_ADDS = 8;
		var num_adds = document.getElementById("genre-wrapper").childElementCount;
		
		if (num_adds < MAX_ADDS)
		{
			$("#genre-wrapper").append('<div><select name="genre[]" class="form-control"><option value="" select disabled hidden>Choose genre</option><option value="Action">Action</option><option value="Adult">Adult</option><option value="Adventure">Adventure</option><option value="Animation">Animation</option><option value="Biography">Biography</option><option value="Comedy">Comedy</option><option value="Crime">Crime</option><option value="Documentary">Documentary</option><option value="Drama">Drama</option><option value="Family">Family</option><option value="Fantasy">Fantasy</option><option value="Film Noir">Film Noir</option><option value="Game-Show">Game-Show</option><option value="History">History</option><option value="Horror">Horror</option><option value="Musical">Musical</option><option value="Music">Music</option><option value="Mystery">Mystery</option><option value="News">News</option><option value="Reality-TV">Reality-TV</option><option value="Romance">Romance</option><option value="Sci-Fi">Sci-Fi</option><option value="Short">Short</option><option value="Sport">Sport</option><option value="Talk-Show">Talk-Show</option><option value="Thriller">Thriller</option><option value="War">War</option><option value="Western">Western</option></select><a href="#" class="remove-field"><span class="material-icons">clear</span></a></div>');
		}
	});
	
	$("#genre-wrapper").on("click", ".remove-field", function(event)
	{
		event.preventDefault();
		$(this).parent("div").remove();
	});
	
	$(".meta-wrapper").on("click", "#favorite-button", function(event)
	{
		event.preventDefault();
		
		$.ajax(
		{
			type: "POST",
			url: "favorite.php?id=" + document.getElementById("favorite-button").parentElement.id,
			data:
			{
				action: "favorite"
			},
			success: function()
			{
				document.getElementById("favorite-button").innerHTML = "<span class=\"material-icons\">favorite</span> Favorited";
				document.getElementById("favorite-button").id = "unfavorite-button";
			}
		});
	});
	
	$(".meta-wrapper").on("click", "#unfavorite-button", function(event)
	{
		event.preventDefault();
		
		$.ajax(
		{
			type: "POST",
			url: "favorite.php?id=" + document.getElementById("unfavorite-button").parentElement.id,
			data:
			{
				action: "favorite"
			},
			success: function()
			{
				document.getElementById("unfavorite-button").innerHTML = "<span class=\"material-icons\">favorite_border</span> Add to Favorites";
				document.getElementById("unfavorite-button").id = "favorite-button";
			}
		});
	});
	
	$(".episode-wrapper").on("click", ".view-season-button", function(event)
	{
		event.preventDefault();
		
		var id = $(this).attr('id').split("_").pop();
		
		document.getElementsByClassName("season-list")[0].parentNode.removeChild(document.getElementsByClassName("season-list")[0]);
		
		for (var i = 0; i < episodeLists.length; i++)
		{
			if (episodeLists[i].id == "season-" + id + "-list")
			{
				document.getElementsByClassName("episode-wrapper")[0].appendChild(episodeLists[i]);
				break;
			}
		}
	});
	
	$(".episode-wrapper").on("click", ".watch-episode", function(event)
	{
		event.preventDefault();
		
		var id = $(this).attr('id');
		
		document.getElementById("player").src = "https://drive.google.com/file/d/" + id + "/preview";
		$("#player-wrapper").addClass("show");
	});
	
	$("#watch").click(function(event)
	{
		event.preventDefault();
		$("#player-wrapper").addClass("show");
	});
	
	$("#player-wrapper a").click(function(event)
	{
		event.preventDefault();
		document.getElementById("player").src = document.getElementById("player").src;
		$("#player-wrapper").removeClass("show");
	});
});

function updateHeight()
{
	var movieItem = $('.movie-item img');
	var showItem = $('.show-item img');
	var albumItem = $('.album-item img');
	var bookItem = $('.book-item img');
	var addItem = $('.add-item img');
	
	var movieWidth = movieItem.width();
	var showWidth = showItem.width();
	var albumWidth = albumItem.width();
	var bookWidth = bookItem.width();
	var addWidth = addItem.width();
	
	movieItem.css('height', movieWidth * 1.5);
	showItem.css('height', showWidth * 1.5);
	albumItem.css('height', albumWidth);
	bookItem.css('height', bookWidth * 1.5);
	addItem.css('height', addWidth * 1.5);
}