$(function () {
	generateMap ();
	bindArrowKeys ();
	bindMovementTypes ();
});

function generateMap (direction) {
	$.ajax ({
		cache: false,
		success: function (returned) {
			if ($('goto', returned).attr ('page') == 'fight') window.location = relroot+'/fight.php';
			
			$('#map_table').html ($('root>map_data', returned).text());
			$('#mapnav').html ($('root>navigation_data', returned).text());
		},
		data: { 'move': direction },
		error: function (jqXHR, textStatus, errorthrown) {
			console.log ("unsuccessful ajax call whilst trying to generate map: "+textStatus+" - "+ errorthrown);
		},
		dataType: "xml",
		type: "post",
		url: relroot+'/map_scripts/draw.php'
	});
}

function bindArrowKeys () {
	$(window).keyup (function (e) {
		var direction;
		// nab the key code rather than the charCode. We can't access charCode anyway in .keyup, but
		// keyCode - which has a code for each key - is what we can't rather than an ascii value for
		// each key (charCode)
		var code = e.keyCode;

		switch (code) {
			case 72:
				direction = "west";
				break;
			case 74:
				direction = "south";
				break;
			case 75:
				direction = "north";
				break;
			case 76:
				direction = "east";
				break;
			case 89:
				direction = "nw";
				break;
			case 85:
				direction = "ne";
				break;
			case 66:
				direction = "sw";
				break;
			case 78:
				direction = "se";
				break;
		}
		
		if (direction != null) generateMap (direction);
	});
}

function bindMovementTypes () {
	$('.move').click (function () {
		// let the server know there's been an update
		$.post (relroot+'/map_scripts/movetype.php', { s: $(this).html() } );
		
		// make sure they all have the .link class
		$('.move').addClass ('link').css ('font-weight', 'normal');
		// remove it from the one just clicked, and make it bold
		$(this).removeClass ('link').css ('font-weight', 'bold');
	});
}