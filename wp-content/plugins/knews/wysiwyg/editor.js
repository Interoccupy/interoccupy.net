var current_editor='';
var current_range='';
var current_node='';
var inside_editor=false;
var can_justify=false;
var justify_node='';
var im_on_link=false;
var link_node='';
var moz_dirty=false;
var bold_type='';
var cursive_type='';
var flash_wysiwyg_first=true;
var preview_img=false;
var preview_css=false;

//function start() {
parent.jQuery(document).ready( function () {
//parent.jQuery(window).load(function() {

	parent.jQuery('select#zoom').change(function () {
		parent.jQuery('body', document).attr('style','zoom: ' + parent.jQuery(this).val() + '; -moz-transform: scale(' + parent.jQuery(this).val() + '); -moz-transform-origin: 0 0;');
	});

	parent.jQuery(document).keyup(function(e) {
		if (e.keyCode == 27 && parent.referer_image_size != '') {
			parent.jQuery(parent.referer_image_size).attr('height', parent.resizing_image_h_undo);
			parent.jQuery(parent.referer_image_size).attr('width', parent.resizing_image_w_undo);
			parent.jQuery(parent.referer_image_size).attr('src', parent.resizing_image_url_undo);

			clean_resize_handlers();
			parent.referer_image_size='';
			parent.referer_image_size_ajax='';
			return false;
		}
	});
	parent.jQuery('.droppable_empty', document)
		.live('mouseover', function() {
			//alert("ooo");
			parent.droppable_over=this;
			parent.jQuery(this).addClass('droppable_empty_hover');
		})
		.live('mouseout', function() {
			parent.droppable_over=null;
			parent.jQuery(this).removeClass('droppable_empty_hover');
		});
	/*1.1.0 parent.jQuery('img.editable', document).live('mouseover', function(e) {
		parent.jQuery(this).prev().css('display','block');
	});
	parent.jQuery('img.editable', document).live('mouseout', function(e) {
		parent.jQuery(this).prev().fadeOut();
	});
	parent.jQuery('span.img_handler', document).live('mouseover', function() {
		parent.jQuery(this).stop().show();
	});*/
	//1.1.0 parent.jQuery('span.img_handler a.change_image', document).live('click', function(e) {
	parent.jQuery('div.wysiwyg_toolbar a.change_image').click(function(e) {
		parent.setCatcher();
		//1.1.0 parent.referer_image=parent.jQuery(this).parent().next();
		parent.tb_show('', 'media-upload.php?type=image&amp;post_id=' + parent.one_post_id + '&amp;TB_iframe=true&amp;width=640&amp;height=' + (parseInt(parent.jQuery(parent.window).height(), 10)-100));
		return false;
	});
	//1.1.0 parent.jQuery('span.img_handler a.rredraw_image', document).live('click', function(e) {
	parent.jQuery('input.rredraw_image').click(function(e) {
	
		//1.1.0 parent.referer_image = parent.jQuery(this).parent().next();
		//1.1.0 parent.callback_img('<img src="' + parent.jQuery(parent.referer_image).attr('src')+ '" />');

		align='';
		if (parent.jQuery(parent.referer_image_size).hasClass('alignable')) {
			align=parent.jQuery('#image_align option:selected').val();
		}

		a = parent.jQuery('textarea#image_alt').val();
		b = parent.jQuery('input#image_b').val();
		hs = parent.jQuery('input#image_hs').val();
		vs = parent.jQuery('input#image_vs').val();

		if (parent.jQuery(parent.referer_image_size).closest('a').length==0) {
			if (parent.jQuery('div.image_properties input#image_link').val() != '') {
				parent.jQuery('a.doing_link_temp', document).removeClass('doing_link_temp');
				parent.jQuery(parent.referer_image_size).before('<a class="doing_link_temp" href="' + parent.jQuery('div.image_properties input#image_link').val() + '"></a>');
				parent.jQuery(parent.referer_image_size).clone().appendTo(parent.jQuery('a.doing_link_temp', document));
				parent.jQuery(parent.referer_image_size).remove();
				parent.referer_image_size = parent.jQuery('a.doing_link_temp img', document)[0];
				//alert(parent.jQuery(parent.referer_image_size).parent().html());
				parent.jQuery('a.doing_link_temp', document).removeClass('doing_link_temp');
			}
		} else {
			if (parent.jQuery('div.image_properties input#image_link').val() == '') {
				parent.jQuery('img.removing_link_temp', document).removeClass('removing_link_temp');
				parent.jQuery(parent.referer_image_size).addClass('removing_link_temp');
				save_content=parent.jQuery(parent.referer_image_size).closest('a').html();
				parent.jQuery(parent.referer_image_size).closest('a').replaceWith(save_content);
				parent.referer_image_size = parent.jQuery('img.removing_link_temp', document)[0];
				//alert(parent.jQuery(parent.referer_image_size).parent().html());
				parent.jQuery('img.removing_link_temp', document).removeClass('removing_link_temp');
			} else {
				parent.jQuery(parent.referer_image_size).closest('a').attr('href', parent.jQuery('div.image_properties input#image_link').val());
			}
		}

		parent.callback_img('<img src="' + parent.jQuery('div.image_properties input#image_url').val() + '" class="align' + align + '" />', a, b, hs, vs, align);

		clean_resize_handlers();
		
		parent.referer_image_size='';
		return false;
	});
	//1.1.0 parent.jQuery('span.img_handler a.uundo_image', document).live('click', function(e) {
	parent.jQuery('input.uundo_image').click(function(e) {

		parent.jQuery(parent.referer_image_size).attr('height', parent.resizing_image_h_undo);
		parent.jQuery(parent.referer_image_size).attr('width', parent.resizing_image_w_undo);
		parent.jQuery(parent.referer_image_size).attr('src', parent.resizing_image_url_undo);

		clean_resize_handlers();
		parent.referer_image_size='';
		parent.referer_image_size_ajax='';
		return false;
	});
	parent.jQuery('a', document).live('click', function(e) {
		return false;
	});
	parent.jQuery('img', document).live('mousedown', function(e) {
		
		if (!parent.jQuery(this).hasClass('editable')) return false;
		
		if (parent.referer_image_size != this && parent.referer_image_size != '') {
			alert(parent.must_apply_undo);
			return false;
		}
		if (parent.referer_image_size != this) {
			flash_wysiwyg(0);
			parent.img_max_width=0; parent.img_min_width=0; parent.img_max_height=0; parent.img_min_height=0;
			var classes = parent.jQuery(this).attr('class');
			classes_array = classes.split(' ');
			for(i=0; i<classes_array.length; i++) {
				cla=classes_array[i];
				if (cla.substr(0,6)=='width_') {
					cla_s=cla.split('_');
					parent.img_min_width=parseInt(cla_s[1],10);
					parent.img_max_width=parseInt(cla_s[2],10); 
				}
				if (cla.substr(0,7)=='height_') {
					cla_s=cla.split('_');
					parent.img_min_height=parseInt(cla_s[1],10);
					parent.img_max_height=parseInt(cla_s[2],10); 
				}
			}
			
			clean_resize_handlers();
			parent.referer_image_size='';
			
			parent.jQuery('div.image_properties').show();
			parent.jQuery('div.tools').hide();
			parent.jQuery('div.save_button').hide();
			parent.jQuery('div.plegable').hide();

			if (parent.jQuery(this).hasClass('alignable')) {
				parent.jQuery('div.alignable').show();
				align=parent.jQuery(this).attr('align');
				if (typeof align === "undefined") align='';
				parent.jQuery("#image_align").val(align);
			} else {
				parent.jQuery('div.alignable').hide();
			}
			
			parent.jQuery('div.image_properties input#image_url').val(look_attr(this, 'src'));
			//parent.jQuery('div.image_properties input#image_w').val(look_attr(this, 'width'));
			//parent.jQuery('div.image_properties input#image_h').val(look_attr(this, 'height'));
			parent.jQuery('div.image_properties textarea#image_alt').val(look_attr(this, 'alt'));
			
			parent.jQuery('div.image_properties input#image_b').val(look_attr(this, 'border'));
			parent.jQuery('div.image_properties input#image_hs').val(look_attr(this, 'hspace'));
			parent.jQuery('div.image_properties input#image_vs').val(look_attr(this, 'vspace'));

			if (parent.jQuery(this).closest('a').length==0) {
				parent.jQuery('div.image_properties input#image_link').val('');
			} else {
				parent.jQuery('div.image_properties input#image_link').val(parent.jQuery(this).closest('a').attr('href'));
			}
	
			//1.1.0 parent.jQuery(this).prev().append('<a href="#" class="rredraw_image" title="' + parent.sharp_image + '"></a>');
			//1.1.0 parent.jQuery(this).prev().append('<a href="#" class="uundo_image" title="' + parent.undo_image + '"></a>');
	
			ww = parseInt(parent.jQuery(this).attr('width'), 10);
			hh = parseInt(parent.jQuery(this).attr('height'), 10);
			tt = parseInt(parent.jQuery(this).offset().top, 10);
			ll = parseInt(parent.jQuery(this).offset().left, 10);

			parent.resizing_image_w_undo=ww;
			parent.resizing_image_h_undo=hh;
			parent.resizing_image_url_undo=parent.jQuery(this).attr('src');

			parent.referer_image_size = this;

			if (typeof parent.jQuery(this).attr('style') === "undefined") {
				parent.resizing_image_style='';
			} else {
				parent.resizing_image_style=parent.jQuery(this).attr('style');
			}

			parent.jQuery(this)
				.css('outline', 'dashed #000 1px')
				.css('position','absolute')
				.css('left',ll)
				.css('top',tt)
				.after('<div style="width:' + ww + 'px; height:' + hh + 'px;" class="image_spacer">&nbsp;</div>');
	
			prohibited_x=''; prohibited_y='';
			if (parent.img_max_width==parent.img_min_width && parent.img_min_width!=0) prohibited_x=' prohibited';
			if (parent.img_max_height==parent.img_min_height && parent.img_min_height!=0) prohibited_y=' prohibited';

			parent.jQuery(this).after('<span class="img_resizer img_resizer_s' + prohibited_y + '" handlertype="s">');
			parent.jQuery(this).after('<span class="img_resizer img_resizer_se' + prohibited_y + prohibited_x + '" handlertype="se">');
			parent.jQuery(this).after('<span class="img_resizer img_resizer_e' + prohibited_x + '" handlertype="e">');
			parent.jQuery(this).after('<span class="img_resizer img_resizer_ne' + prohibited_y + prohibited_x + '" handlertype="ne">');
			parent.jQuery(this).after('<span class="img_resizer img_resizer_n' + prohibited_y + '" handlertype="n">');
			parent.jQuery(this).after('<span class="img_resizer img_resizer_nw' + prohibited_y + prohibited_x + '" handlertype="nw">');
			parent.jQuery(this).after('<span class="img_resizer img_resizer_w' + prohibited_x + '" handlertype="w">');
			parent.jQuery(this).after('<span class="img_resizer img_resizer_sw' + prohibited_y + prohibited_x + '" handlertype="sw">');
			
			move_resize_handlers(ww, hh, tt, ll);
		}
		return false;
	});
	parent.jQuery('span.img_resizer', document)
		.live('mousedown', function(e) {

			ww = parseInt(parent.jQuery(parent.referer_image_size).attr('width'), 10);
			hh = parseInt(parent.jQuery(parent.referer_image_size).attr('height'), 10);
			tt = parseInt(parent.jQuery(parent.referer_image_size).offset().top, 10);
			ll = parseInt(parent.jQuery(parent.referer_image_size).offset().left, 10);

			parent.resizing_image_t=tt;
			parent.resizing_image_l=ll;
			parent.resizing_image_w=ww;
			parent.resizing_image_h=hh;

			parent.resizing_image=parent.jQuery(this).attr('handlertype');
			
			parent.resizing_image_x=parent.ratoliX;
			parent.resizing_image_y=parent.ratoliY;
			
			parent.resizing_image_handler_x=parent.jQuery(this).offset().left;
			parent.resizing_image_handler_y=parent.jQuery(this).offset().top;

			return false;
		})
		.live('mouseup', function(e) {
			parent.resizing_image='';
			return false;
		})
		.live('click', function(e) {
			return false;
		});

	parent.jQuery(document).bind('keypress keydown click', function() {
		update_editor();
	});

	/*parent.jQuery('.content_editable')
		.focus( function(e) {
			current = this;
			e.preventDefault();
		});*/

//}
});

function look_attr(obj, nameattr) {
	if (typeof parent.jQuery(obj).attr(nameattr) == 'undefined') return '';
	return parent.jQuery(obj).attr(nameattr);
}
function flash_wysiwyg(n) {
	n=parseInt(n)+1;
	if (n==1 || n==3 || n==5) {
		parent.jQuery('div.image_properties').css('background','#ff0');
	} else {
		parent.jQuery('div.image_properties').css('background','none');
	}
	if (flash_wysiwyg_first || n<2) {
		if (n<6) {
			setTimeout('flash_wysiwyg('+n+')', 50);
		} else {
			flash_wysiwyg_first=false;
		}
	}
}
function test_browser() {
	//alert("tb");
	parent.jQuery('div.wysiwyg_editor', document)
		.append('<p id="testbrowser" contenteditable="true">test</p>');
		
	selecttag(parent.jQuery('#testbrowser', document)[0]);
	var returnValue=document.execCommand('Bold',false,null);
	var returnValue=document.execCommand('Italic',false,null);

	if (parent.jQuery('#testbrowser strong', document).length == 1) {
		bold_type='strong';
	} else if (parent.jQuery('#testbrowser b', document).length == 1) {
		bold_type='b';
	} else {
		moz_dirty=true;
	}

	if (parent.jQuery('#testbrowser em', document).length == 1) {
		cursive_type='em';
	} else if (parent.jQuery('#testbrowser i', document).length == 1) {
		cursive_type='i';
	} else {
		moz_dirty=true;
	}
	
	parent.jQuery('#testbrowser', document).remove();
	
	//alert (bold_type);
	//alert (cursive_type);
	browserize_html(document);
}

function listen_module (module) {
		
	parent.jQuery('span.handler span.move', module).mousedown( function(e) {

			if (parent.referer_image_size != this && parent.referer_image_size != '') {
				alert(parent.must_apply_undo);
				e.preventDefault();
				return false;
			}
			parent.not_saved();
			parent.move_item=parent.jQuery(this).closest('.draggable');

			parent.jQuery(parent.move_item).closest('.droppable').prev().addClass('droppable_empty_hidden');
			parent.jQuery(parent.move_item).closest('.droppable').next().addClass('droppable_empty_hidden');
//LLLL
			parent.move_item.clone().appendTo(parent.jQuery('div.drag_preview'));

			parent.move_preview=true; parent.update_preview();

			parent.zone=parent.look_zone(parent.move_item);
			
			parent.jQuery('.droppable_empty', document).children().html('&nbsp;');
			
			if (parent.zone != 0) {
				parent.jQuery('body', document).addClass('doing_drag');
				parent.jQuery('.droppable_empty', document).hide();
				parent.jQuery('.container_zone_' + parent.zone + ' .droppable_empty', document).show();
			} else {
				parent.jQuery('body', document).addClass('doing_drag');
				parent.jQuery('.droppable_empty', document).show();
			}

			e.preventDefault();
		});
	
	parent.jQuery('span.handler span.delete', module).click( function(e) {
			if (parent.referer_image_size != this && parent.referer_image_size != '') {
				alert(parent.must_apply_undo);
				return false;
			}

			parent.referer_delete = this;
			parent.tb_dialog('Knews', parent.confirm_delete, parent.button_yes, parent.button_no, 'deleteModule');
		});
	parent.jQuery(module).mouseover( function(e) {
			yy=parseInt(parent.jQuery(this).offset().top, 10)-30;
			if (yy<3) yy=3;
			parent.jQuery('span.handler', this)
				.css('left', parseInt(parent.jQuery(this).offset().left, 10))
				.css('top', yy);
			//;
		});

	/*parent.jQuery('.content_editable', module)
		.focus( function(e) {
			current = this;
			e.preventDefault();
		});*/
	
}
function look_scroll() {
	ratoliX_tb = parseInt(parent.jQuery(window).scrollLeft(), 10);
	ratoliY_tb = parseInt(parent.jQuery(document).scrollTop(), 10);
	return Array(ratoliX_tb, ratoliY_tb);
}

var tagsnav=Array();

function restore_focus() {
	if (inside_editor) {
		parent.jQuery(current_editor).focus();
	}
}
function saveSelection() {
    if (window.getSelection) {
        sel = window.getSelection();
        if (sel.getRangeAt && sel.rangeCount) {
            return sel.getRangeAt(0);
        }
    } else if (document.selection && document.selection.createRange) {
        return document.selection.createRange();
    }
    return null;
}

function restoreSelection(range) {
    if (range) {
        if (window.getSelection) {
            sel = window.getSelection();
            sel.removeAllRanges();
            sel.addRange(range);
        } else if (document.selection && range.select) {
            range.select();
        }
    }
}

function browserize_html(context) {
	if (moz_dirty) {
		replace_tag('strong','span',context,'fontWeight','bold');
		replace_tag('b','span',context,'fontWeight','bold');
		replace_tag('em','span',context,'fontStyle','italic');
		replace_tag('i','span',context,'fontStyle','italic');
	} else {
		if (bold_type=='b') {
			replace_tag('strong','b','','');
		} else {
			replace_tag('b','strong','','');
		}
		if (cursive_type=='i') {
			replace_tag('em','i','','');
		} else {
			replace_tag('i','em','','');
		}
	}
}

function normalize_html() {
	if (moz_dirty) {
		var changes=1;
		while (changes != 0) {
			changes=0;
			parent.jQuery(".content_editable span", document).each(function() {
				if (parent.look_for_css_property(parent.jQuery(this).attr('style'), 'font-weight')) {
					if (parent.jQuery(this).css('fontWeight') == 'bold' || parseInt(parent.jQuery(this).css('fontWeight'), 10) > 400) {
						parent.jQuery(this).replaceWith('<b>' + parent.jQuery(this).html() + '</b>');
						changes++;
						return false;
					}
				}
				if (parent.look_for_css_property(parent.jQuery(this).attr('style'), 'font-style')) {
					if (parent.jQuery(this).css('fontStyle') == 'italic') {
						parent.jQuery(this).replaceWith('<i>' + parent.jQuery(this).html() + '</i>');
						changes++;
						return false;
					}
				}
				if (parent.look_for_css_property(parent.jQuery(this).attr('style'), 'text-decoration')) {
					if (parent.jQuery(this).css('textDecoration') == 'line-through') {
						parent.jQuery(this).replaceWith('<i>' + parent.jQuery(this).html() + '</i>');
						changes++;
						return false;
					}
				}
			});
		}
	}
	replace_tag('strong','b',document,'','');
	replace_tag('em','i',document,'','');
}

function replace_tag(findtag, replacetag, context, mdname, mdvalue) {
	parent.jQuery('.content_editable ' + findtag , context).each(function() {

		tag_content=parent.jQuery(this).html();
		tag_class=parent.jQuery(this).attr('class');
		tag_style=parent.jQuery(this).attr('style');

		parent.jQuery(this).replaceWith('<' + replacetag + ' class="knews_replacing">' + tag_content + '</' + replacetag + '>');
		if (tag_style != '') parent.jQuery(replacetag + '.knews_replacing', context).attr('style',tag_style);
		if (replacetag=='span') parent.jQuery(replacetag + '.knews_replacing', context).css(mdname,mdvalue);
			
		if (tag_class !='') {
			parent.jQuery('span.knews_replacing', context)
				.removeClass('knews_replacing')
				.addClass(tag_class);
		} else {
			parent.jQuery('span.knews_replacing', context).removeAttr('class');
		}
			
	});
}

function firefox_separate(container) {
	if (moz_dirty) {
		parent.jQuery('span', container).each(function() {
			attribs = parent.jQuery(this).attr('style');
			attribs = attribs.split(' ').join('');
			attribs=attribs.split(';');
	
			for (var x=0; x<attribs.length; x++) {
				if (attribs[x]=='' || attribs[x]==';') {
					attribs.splice(x, 1);
					x=x-1;
				}
			}
	
			if (attribs.length > 1) {
				code=parent.jQuery(this).html();
				//alert(parent.jQuery(this).parent().html());
				for (var x=1; x<attribs.length; x++) {
					code='<span style="' + attribs[x] + ';">' + code + '</span>';
				}
				parent.jQuery(this).attr('style',attribs[0]).html(code);
			}
		});
	}
}
function find_tag(node) {
	nocrash=0;
	while (node.tagName==undefined && nocrash<20) {
		nocrash++;
		node=node.parentNode;
	}
	return node;
}
/*parent.jQuery(document).ready(function() {
	//parent.jQuery('body > table').bind('click', function() {
		//alert("mouse");
	//});

})*/
function update_editor() {
	inside_editor=false;
	can_justify=false;
	im_on_link=false;
	var tags='';
	var ntag=0;
	var continue_loop=true;
	
//if (document.selection) { alert ('ds'); }
//if (window.getSelection) { alert ('gs'); }

	if (document.selection) {
		var selection = document.selection.createRange();
		current_node = selection.parentElement();
//alert(current_node);
//alert(current_node.innerHTML);
	} else if (window.getSelection) {
		
		var selection = window.getSelection(); //what the user has selected
		if (selection.rangeCount == 0) return;
		current_range = selection.getRangeAt(0); //the first range of the selection

		if (current_range.startContainer == current_range.endContainer) {
			current_node = current_range.startContainer;
		} else {
			current_node = current_range.commonAncestorContainer;
		}

	} else {
		return;
	}

	
	var in_node = current_node;

	while (continue_loop) {
		if (in_node.tagName!=undefined) {
			tag_name = in_node.tagName;

			if (tag_name=='SPAN' && moz_dirty) {
				spanstyle=parent.jQuery(in_node).attr('style');
				if (typeof spanstyle !== "undefined") {
					spanstyle=spanstyle.split(' ').join('');
					spanstyle=spanstyle.split(';').join('');
	
					if (spanstyle=='font-weight:bold') tag_name='b';
					if (spanstyle=='font-style:italic') tag_name='i';
					if (spanstyle=='text-decoration:line-through') tag_name='stroke';
				}
			}

			if (tag_name=='STRONG') tag_name='b';				
			if (tag_name=='EM') tag_name='i';				
			
			if (in_node.className == 'content_editable') {

				inside_editor=true;
				current_editor=in_node;
				continue_loop=false;
			
			} else {

				tags = '<a href="#" onclick="selecttag(' + ntag + '); return false;">&lt;' + tag_name.toLowerCase() + '&gt;</a> ' + tags;
				tagsnav[ntag]=in_node;
				ntag++;
	
				if (in_node.tagName == 'TD' || in_node.tagName == 'P' ) {
					can_justify=true;
					justify_node=in_node;
				}
				
				if (in_node.tagName == 'A' ) {
					im_on_link=true;
					link_node=in_node;
				}
				
				if (in_node.tagName == 'BODY') continue_loop=false;
			}
		}
		in_node = in_node.parentNode;
	}
	if (inside_editor) {

		parent.jQuery('#tagsnav').html(tags);
		parent.jQuery('#botonera a.color').css('backgroundColor', '#' + parent.rgb2hex(parent.jQuery(find_tag(current_node)).css('color')));
		parent.jQuery('#botonera div.standard_buttons').removeClass('desactivada');

		if (can_justify) {
			parent.jQuery('#botonera div.justify_buttons').removeClass('desactivada');
		} else {
			parent.jQuery('#botonera div.justify_buttons').addClass('desactivada');
		}

	} else {

		parent.jQuery('#tagsnav').html('');
		parent.jQuery('#botonera a.color').css('backgroundColor', '#888888');
		parent.jQuery('#botonera div.standard_buttons').addClass('desactivada');
		parent.jQuery('#botonera div.justify_buttons').addClass('desactivada');

	}
}

function selecttag_n(n) { selecttag(tagsnav[n]); }

function selecttag(obj) {
	if (document.selection) {
        var textRange = document.body.createTextRange();
        textRange.moveToElementText(obj);
        textRange.select();
    } else if (window.getSelection) {
        var sel = window.getSelection();
        sel.removeAllRanges();
        var range = document.createRange();
        range.selectNodeContents(obj);
        sel.addRange(range);
    }

	restore_focus();
}

function b_color(hex) {
	if (inside_editor) {
		if (im_on_link) {
			parent.jQuery(link_node).css('color',hex);
		} else {
			document.execCommand('ForeColor',false, hex);
		}
		restore_focus();
	}
}
function b_simple(action) {
	if (inside_editor) {
		//container = parent.jQuery(find_tag(info_node().commonAncestorContainer)).closest('span.content_editable');
		var returnValue = document.execCommand(action,false,null);
		firefox_separate(current_editor);
		restore_focus();
		update_editor();
	}
}
function b_link() {
	if (inside_editor) {

		url = '';
		if (im_on_link) url = parent.jQuery(link_node).attr('href');

		if (url = prompt('Link URL:', url)) {

			if (im_on_link) {
				if (url!='') parent.jQuery(link_node).attr('href',url);
			} else {
				document.execCommand('createlink',false,url);
			}
		}
		restore_focus();
		update_editor();
	}
}
function b_insert_image(img_url, align) {
	if (inside_editor) {
		parent.jQuery('img', document).addClass('no_target');
		restoreSelection(parent.saved_range);
		if (document.execCommand('InsertImage', false, img_url)) {
			restoreSelection(parent.saved_range);
			parent.jQuery('img', document).not('.no_target').each(function() {
				parent.jQuery(this)
					.load(function() {
						parent.jQuery(this)
							.addClass('editable alignable')
							.attr('height',parent.jQuery(this).height())
							.attr('width',parent.jQuery(this).width());
							if (align != '') parent.jQuery(this).attr('align', align);
					});
					
			});
		}

		parent.jQuery('img', document).removeClass('no_target');
		restore_focus();
		update_editor();	
	}
}
function b_del_link() {
	if (inside_editor) {

		if (im_on_link) {
			deleting_node = parent.jQuery(current_node).parent();

			if (parent.jQuery(deleting_node).is('a')) {
				parent.jQuery(deleting_node).replaceWith(parent.jQuery(deleting_node).html());
			}
		}

		restore_focus();
		update_editor();
	}
}
function b_justify(justify) {
	if (inside_editor) {
		
		if (can_justify) parent.jQuery(justify_node).attr('align',justify);

		restore_focus();
		update_editor();
	}
}

function b_preview(what) {
	if (what=='img') {
		
		if (!preview_img) {
			parent.jQuery('a.previewIMG').removeClass('previewIMG').addClass('previewIMG_on');
		} else {
			parent.jQuery('a.previewIMG_on').removeClass('previewIMG_on').addClass('previewIMG');			
		}
		
		parent.jQuery('img', document).each(function() {
			
			src_now=parent.jQuery(this).attr('src');

			if (!preview_img) {
				parent.jQuery(this).attr('src', 'http://www.previewoff.com/'+src_now);
			} else {
				parent.jQuery(this).attr('src', src_now.replace('http://www.previewoff.com/',''));
			}
		});

		parent.jQuery('*', document).not('.droppable_empty, .droppable, span.chooser, span.chooser *, span.handler, span.handler *').each(function() {

			if (!preview_img) {
				
				var attr = parent.jQuery(this).attr('style');
				if (typeof attr !== 'undefined' && attr !== false) {
					savestyle=parent.jQuery(this).attr('style');
					savestyle=parent.absoluteReplace(savestyle, 'url', 'previewoffurl');
					parent.jQuery(this).attr('style', savestyle);
				}
				
				var attr = parent.jQuery(this).attr('background');
				if (typeof attr !== 'undefined' && attr !== false) {
					parent.jQuery(this).attr('savebackground', parent.jQuery(this).attr('background'));
					parent.jQuery(this).removeAttr('background');
				}
			} else {
				var attr = parent.jQuery(this).attr('style');
				if (typeof attr !== 'undefined' && attr !== false) {
					savestyle=parent.jQuery(this).attr('style');
					savestyle=parent.absoluteReplace(savestyle, 'previewoffurl', 'url');
					parent.jQuery(this).attr('style', savestyle);
				}
				
				var attr = parent.jQuery(this).attr('savebackground');
				if (typeof attr !== 'undefined' && attr !== false) {
					parent.jQuery(this).attr('background', parent.jQuery(this).attr('savebackground'));
					parent.jQuery(this).removeAttr('savebackground');
				}
			}
		});

		preview_img=!preview_img;

	} else if (what=='css') {

		if (!preview_css) {
			parent.jQuery('a.previewCSS').removeClass('previewCSS').addClass('previewCSS_on');
		} else {
			parent.jQuery('a.previewCSS_on').removeClass('previewCSS_on').addClass('previewCSS');			
		}

		parent.jQuery('*', document).not('.droppable_empty, .droppable, span.chooser, span.chooser *, span.handler, span.handler *').each(function() {

			if (!preview_css) {
				
				var attr = parent.jQuery(this).attr('style');
				if (typeof attr !== 'undefined' && attr !== false) {
					savestyle=parent.jQuery(this).attr('style');
					parent.jQuery(this).attr('savestyle', savestyle);
					parent.jQuery(this).removeAttr('style');
				}
				
			} else {
				var attr = parent.jQuery(this).attr('savestyle');
				if (typeof attr !== 'undefined' && attr !== false) {
					savestyle=parent.jQuery(this).attr('savestyle');
					parent.jQuery(this).attr('style', parent.jQuery(this).attr('savestyle'));
					parent.jQuery(this).removeAttr('savestyle');
				}
				
			}
		});
		preview_css=!preview_css;
	} else if (what == 'save') {
		if (preview_img) b_preview('img');
		if (preview_css) b_preview('css');
	}
}

function move_resize_handlers(ww, hh, tt, ll) {
	parent.jQuery('span.img_resizer_s', document).css('left', ll + Math.floor(ww/2) - 4).css('top', tt + hh - 4);
	parent.jQuery('span.img_resizer_se', document).css('left', ll + ww - 4).css('top', tt + hh - 4);
	parent.jQuery('span.img_resizer_e', document).css('left', ll + ww - 4).css('top', tt + Math.floor(hh/2) - 4);
	parent.jQuery('span.img_resizer_ne', document).css('left', ll + ww - 4).css('top', tt - 4);
	parent.jQuery('span.img_resizer_n', document).css('left', ll + Math.floor(ww/2) - 4).css('top', tt - 4);
	parent.jQuery('span.img_resizer_nw', document).css('left', ll - 4).css('top', tt - 4);
	parent.jQuery('span.img_resizer_w', document).css('left', ll - 4).css('top', tt + Math.floor(hh/2) - 4);
	parent.jQuery('span.img_resizer_sw', document).css('left', ll - 4).css('top', tt + hh - 4);
	
	parent.jQuery(parent.referer_image_size).prev().css('left', ll).css('top', tt);
}

function clean_resize_handlers() {

	//1.1.0 parent.jQuery('a.rredraw_image, a.uundo_image', document).remove();

	parent.jQuery('span.img_resizer',document).remove();
	parent.jQuery('div.image_spacer', document).remove();

	if (parent.referer_image_size!='') {
		parent.jQuery(parent.referer_image_size).attr('style', parent.resizing_image_style);

		tt = parseInt(parent.jQuery(parent.referer_image_size).offset().top, 10);
		ll = parseInt(parent.jQuery(parent.referer_image_size).offset().left, 10);
		//1.1.0 parent.jQuery(parent.referer_image_size).prev().css('left', ll).css('top', tt);
	}

	parent.jQuery('div.image_properties').hide();
	parent.jQuery('div.tools').show();
	parent.jQuery('div.save_button').show();
	parent.jQuery('div.plegable').show();
}

