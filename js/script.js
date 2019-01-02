var vm_queue = [];


/*$grid.imagesLoaded().progress( function() {
  $grid.masonry('layout');
});*/


jQuery(window).load(function() {
	jQuery(".masonry_video_main").fadeIn();
	jQuery(".masonry_vid_loader").fadeOut();

	build_queue();
	var $grid = jQuery('.masonry_video_main').masonry({
	  // options
	  itemSelector: '.masongry_grid',
	  //columnWidth: 386,
	  gutter: 4,
	  percentPosition: true

	});
	if(jQuery('.masongry_grid').length ) {

		muteAllVideos();

		//var first_vid = jQuery('.masongry_grid').first();

		first_vid_div = findNext();
		//var parnet = first_vid.parent().parent();
		play_the_vid(first_vid_div);

	 	/*jQuery('.masongry_grid video').on('ended',function(){
	      jQuery(this).parent().parent().addClass("video_has_ended")
	      jQuery(this).parent().parent().removeClass("vid_playing")
	 		var next = findNext(jQuery(this));
	 		if(next) {
	 			playVid(next);
	 		}
	    });*/
	}

/* 	jQuery('.masongry_grid video').on('click', function(){
 	})*/

 	mv_set_poster();

});


jQuery(window).resize(mv_set_poster);

function mv_set_poster() {

	if(jQuery(window).width() < 800 ) {

		jQuery(".masonry_vid video").each(function() {
			var poster = jQuery(this).attr("data-poster");
			jQuery(this).attr("poster" , poster);
		});
	
	}
	else {

		jQuery(".masonry_vid video").each(function() {
			var poster = jQuery(this).attr("data-poster");
			jQuery(this).attr("poster" , "");
		});

	}
}

//vid is the div .masongry_grid
function play_the_vid(vid) {

	console.log(vid);
	vid.addClass("no_overlay");
	vid.removeClass("not_started");
	var videoTag = vid.find("video").get(0);	
	var overlay_after = vid.attr("data-overlay-after");
	overlay_after = overlay_after*1000;

	var cta_after = vid.attr("data-cta-after");
	cta_after = cta_after*1000;

	var next_vid_duration = vid.attr("data-next-vid-duration");
	next_vid_duration = next_vid_duration*1000;

	var volume = vid.attr("volume");
	if(jQuery(window).width() < 780) {
		jQuery('html,body').animate({ scrollTop: jQuery(videoTag).offset().top -30 }, 'fast');
	}

	videoTag.play();
	videoTag.volume = volume;
	console.log("1");
	window.setTimeout(function() {

		vid.removeClass("no_overlay");
		vid.addClass("state_show_overlay");
		console.log("2");
		
		window.setTimeout(function() {
			console.log("3");
			vid.addClass("state_show_cta");
			vid.removeClass("state_show_overlay");

			vid.find(".vid_p").fadeOut( 500, function() {
				window.setTimeout(function() {
					vid.find(".vid_cta").fadeIn(1000);
				},1000)
			});

			window.setTimeout(function() {

				vid.addClass("state_video_ended");
				//vid.removeClass("state_show_cta");
				console.log("4");
				videoTag.pause();
				var nextVidDiv = findNext();
				play_the_vid(nextVidDiv);
				
			}, next_vid_duration);

		} , cta_after);

	}, overlay_after);


}



function findNext() {
	/*var next = _this.parent().parent().next();
	if(next) {
		return next.find("video");
	}
	else {
		return false;
	}*/
	var el = vm_queue.shift();
	return jQuery(".rand_"+el.class);

}

function build_queue() {

	jQuery(".masongry_grid").each(function() {
		var rand = Math.floor(Math.random()*10000000000);
		jQuery(this).addClass("rand_"+rand);
		vm_queue.push({class: rand , order: jQuery(this).attr("data-play-order") } );

	});


	//console.log(vm_queue);
	vm_queue.sort(function(a, b) {
    	return a.order>b.order; 
	})
	console.log(vm_queue);
	
}

function playVid(vid) {
	vid.parent().parent().addClass("vid_playing");
	vid.parent().parent().removeClass("not_started");
	vid.get(0).play();
}

function muteAllVideos() {
	jQuery('.masongry_grid video').each(function() {
		jQuery(this).get(0).volume=0;
	})
}


jQuery(window).resize(change_height_for_boxes);
jQuery(window).load(change_height_for_boxes);

function change_height_for_boxes() {
	jQuery(".masongry_grid").each(function() {
		var vid_height = jQuery(this).find(".masonry_vid").height();
		jQuery(this).find(".masonry_text").height(vid_height);
	})
}