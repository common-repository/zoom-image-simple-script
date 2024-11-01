// Zoomio jQuery Image Zoom script
// By Dynamic Drive: http://www.dynamicdrive.com
// June 14th 17'- Updated to v2.0.4, which adds:


;(function($){
	var defaults = {fadeduration:500}
	var $ziss_container, $ziss_loadingdiv
	var currentzoominfo = { $zoomimage:null, offset:[,], settings:null, multiplier:[,] }
	var $curimg = $()
	var ismobile = navigator.userAgent.match(/(iPad)|(iPhone)|(iPod)|(android)|(webOS)/i) != null

	function getDimensions($target)
	{
		return {w:$target.width(), h:$target.height()}
	}

	function getoffset(what, offsettype)
	{
		return (what.offsetParent)? what[offsettype]+getoffset(what.offsetParent, offsettype) : what[offsettype]
	}

	function zoomio($img, settings)
	{
		var s = settings || defaults
		var trigger = ismobile? 'click' : 'mouseenter'
		$img.off(trigger).on(trigger, function(e){
			if ($ziss_container.css('visibility') == 'visible' && $ziss_container.queue('fx').length ==1 && $curimg == $img)
			{
				return
			}
			$curimg = $img
			var jqueryevt = e
			var e = jqueryevt.originalEvent.changedTouches? jqueryevt.originalEvent.changedTouches[0] : jqueryevt
			var offset
			if (settings.fixedcontainer == true)
			{
				var eloffset = $img.offset()
				offset = {left:eloffset.left, top:eloffset.top}
			}
			else
			{
				offset = {left:getoffset($img.get(0), 'offsetLeft'), top:getoffset($img.get(0), 'offsetTop') }
			}
			var mousecoord = [e.pageX - offset.left, e.pageY - offset.top]
			var imgdimensions = getDimensions($img)
			var containerwidth = s.w || imgdimensions.w
			var containerheight = s.h || imgdimensions.h
			$ziss_container.stop().css({visibility: 'hidden'})
			var $zoomimage
			var zoomdfd = $.Deferred()
			var $targetimg = $img.attr('data-largesrc') || $img.data('largesrc') || $img.attr('src')
			if ($img.data('largesrc'))
			{
				$ziss_loadingdiv.css({width:imgdimensions.w, height:imgdimensions.h, left:offset.left, top:offset.top, visibility:'visible', zIndex:10000})
			}
			$ziss_container.html( '<img src="' + $targetimg + '">' )
			$zoomimage = $ziss_container.find('img')
			if ($zoomimage.get(0).complete)
			{
				zoomdfd.resolve()
			}
			else
			{
				$zoomimage.on('load', function(){
					zoomdfd.resolve()
				})
			}
			zoomdfd.done(function(){
				$ziss_container.css({width:containerwidth, height:containerheight, left:offset.left, top:offset.top})
				var zoomiocontainerdimensions = getDimensions($ziss_container)
				if (settings.scale){
					$zoomimage.css({width: $img.width() * settings.scale})
				}
				var zoomimgdimensions = getDimensions($zoomimage)
				$ziss_loadingdiv.css({zIndex: 9998})
				$ziss_container.stop().css({visibility:'visible', opacity:0}).animate({opacity:1}, s.fadeduration, function(){
					$ziss_loadingdiv.css({visibility: 'hidden'})
				})
				if (ismobile)
				{
					var scrollleftpos = (mousecoord[0] / imgdimensions.w) * (zoomimgdimensions.w - zoomiocontainerdimensions.w)
					var scrolltoppos = (mousecoord[1] / imgdimensions.h) * (zoomimgdimensions.h - zoomiocontainerdimensions.h)
					$ziss_container.scrollLeft( scrollleftpos )
					$ziss_container.scrollTop( scrolltoppos )
				}
				currentzoominfo = {$zoomimage:$zoomimage, offset:offset, settings:s, multiplier:[zoomimgdimensions.w/zoomiocontainerdimensions.w, zoomimgdimensions.h/zoomiocontainerdimensions.h]}
			})

			$img.off('mouseleave').on('mouseleave', function(e){
				if (zoomdfd.state() !='resolved')
				{
					zoomdfd.reject()
					$ziss_loadingdiv.css({visibility: 'hidden'})
				}
			})
			jqueryevt.stopPropagation()
		})		
	}

	$.fn.zoomio = function(options){
		var s = $.extend({}, defaults, options)

		return this.each(function(){
			var $target = $(this)

			$target = ($target.is('img'))? $target : $target.find('img:eq(0)')
			if ($target.length == 0){
				return true
			}
			zoomio($target, s)
		})

	}

	$(function(){
		$ziss_container = $('<div id="zisscontainer">').appendTo(document.body)
		$ziss_loadingdiv = $('<div id="zissloadingdiv"><div class="spinner"></div></div>').appendTo(document.body)
		if (!ismobile)
		{
			$ziss_container.on('mouseenter', function(e){
			})
			$ziss_container.on('mousemove', function(e){
				var $zoomimage = currentzoominfo.$zoomimage
				var imgoffset = currentzoominfo.offset
				var mousecoord = [e.pageX-imgoffset.left, e.pageY-imgoffset.top]
				var multiplier = currentzoominfo.multiplier
				$zoomimage.css({left: -mousecoord[0] * multiplier[0] + mousecoord[0] , top: -mousecoord[1] * multiplier[1] + mousecoord[1]})
			})
			$ziss_container.on('mouseleave', function(){
				$ziss_loadingdiv.css({visibility: 'hidden'})
				$ziss_container.stop().animate({opacity:0}, currentzoominfo.settings.fadeduration, function(){
					$(this).css({visibility:'hidden', left:'-100%', top:'-100%'})
				})
			})
		}
		else
		{
			$ziss_container.addClass('mobileclass')
			$ziss_container.on('touchstart', function(e){
				e.stopPropagation()
			})
			$(document).on('touchstart.dismisszoomio', function(e){
				if (currentzoominfo.$zoomimage)
				{
					$ziss_loadingdiv.css({visibility: 'hidden'})
					$ziss_container.stop().animate({opacity:0}, currentzoominfo.settings.fadeduration, function(){
						$(this).css({visibility:'hidden', left:'-100%', top:'-100%'})
					})
				}
			})
		}
	})

})(jQuery);
