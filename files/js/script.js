//main initialisation
jQuery(document).ready(function() {
	jQuery('#folders .delete').click(
		function(){flddeletebtn($(this))}
	);
	jQuery('#files .delete').click(
		function(){fledeletebtn($(this))}
	);
	//cancel button in modal
	jQuery('#modal-from-dom button.cancel').click(
		function(){
			$('#modal-from-dom').modal('hide');
			$('.modal-backdrop').remove();
		}
	);
	//folder browser
	jQuery('#folders li').click(
		function(){
			getFolders($(this).attr('id'));
			getFiles($(this).attr('id'));
		}
	);
	
});

//load folders
function getFolders(inode){
	var url = 'files.php?tmpl=ajax&action=getfolders&file='+inode;
	//
	$('#folder_loading').show('slow',function(){
	$.ajax({
	  type: "GET",
		url: url,
		dataType: "xml",
		success: function(xml) {
			//reset list
			$('#folders').find('li').remove().end();
			$('#folders').find('button').remove().end();
			var pane = $('#folders')
			var api = pane.data('jsp');
			$(xml).find('folders').each(function(){
				$(this).find('folder').each(function(){
					var id = $(this).attr('id');
					var text = $(this).text();
					var li = $(document.createElement('li')).attr("id",id).text(text);
					api.getContentPane().append(li);
					if(text != '../'){
						var button = $(document.createElement("button")).attr("id",id).attr("class","btn danger delete").text('X');
						api.getContentPane().append(button);
					}
				});
			});
			//reset scroll
			var settings = {showArrows: true};
			$('#folders').jScrollPane();
			//list is clickable
			jQuery('#folders li').click(function(){getFolders($(this).attr('id'));getFiles($(this).attr('id'));});
			jQuery('#folders .delete').click(function(){flddeletebtn($(this));});
			$('#folder_loading').hide('slow',function(){$('#folders li').fadeTo('slow', 1);});
		}
	});
	});
	
}
function getFiles(filename){
	var url = 'files.php?tmpl=ajax&action=getfiles&file='+filename;
	$.ajax({
	  type: "GET",
		url: url,
		dataType: "xml",
		success: function(xml) {
			//reset scroll
			$('#files').find('li').remove().end();
			var pane = $('#files')
			var api = pane.data('jsp');
			
			$(xml).find('files').each(function(){
				$(this).find('file').each(function(){
					var id = $(this).attr('id');
					var text = $(this).text();
					var li = $(document.createElement("li")).attr("id",id);
					var input = $(document.createElement("input")).attr("name",'file').attr("type","checkbox").attr("id",id).attr("value",text);
					var label = $(document.createElement("label")).attr("for",id).text(text);
					var span = $(document.createElement("span")).attr("class","actions");
					var button = $(document.createElement("button")).attr("id",id).attr("class","delete").text('X');
					li.append(input);li.append(label);span.append(button);li.append(span);
					api.getContentPane().append(li);
				});
			});
			//reset scroll
			var settings = {showArrows: true};
			$('#files').jScrollPane();
			jQuery('#files .delete').click(function(){fledeletebtn($(this));});
		}
	});
	
}
function deleteFolder(inode){
	var url = 'files.php?tmpl=ajax&action=deleteFolder&inode='+inode;
	$.get(url, function(data) {
  		if(data == 'success'){
  			message('success','Folder Deleted');
			$('#folders #'+inode).remove();
			$('#folders #'+inode).remove();
			$('#folders').jScrollPane();
  		} else {
  			message('error','Error Deleting Folder');
  		}
});
	
}
function deleteFile(inode){
	var url = 'files.php?tmpl=ajax&action=deleteFile&inode='+inode;
	$.get(url, function(data) {
  		if(data == 'success'){
  			message('success','File Deleted');
			$('#files #row'+inode).remove();
			$('#files').jScrollPane();
  		} else {
  			message('error','Error Deleting File');
  		}
	});
}
function message(type,message){
	jQuery('#message').html(message+'<span id="close">X</span>');
	jQuery('#message').attr('class',type);
	jQuery('#message').show('slow');
}


function flddeletebtn(that){
	var text = $('#'+$(that).attr('id')).text();
	var id =$(that).attr('id');
	alert(id);
	$('#modal-from-dom h3').text('Do you want to delete '+text+' folder?');
	$('#modal-from-dom .modal-body p').text('this will delete all files including all the files and folders inside this folder!');
	$('#modal-from-dom').modal({backdrop: "static"});
	$('#modal-from-dom').modal('show');
	jQuery('#modal-from-dom button.confirm').click(
		function(){
			$('.modal-backdrop').remove();
			$('#modal-from-dom').modal('hide');
			deleteFolder(id);
		}
	);
}
function fledeletebtn(that){
	var id = $(that).attr('id').replace('btn','');
	var text = $('#lbl'+id).text();
	$('#modal-from-dom h3').text('Do you want to delete '+text+' file?');
	$('#modal-from-dom .modal-body p').text('this will remove the file permenantly from the server!');
	$('#modal-from-dom').modal({backdrop: "static"});
	$('#modal-from-dom').modal('show');
	jQuery('#modal-from-dom button.confirm').click(
		function(){
			$('.modal-backdrop').remove();
			$('#modal-from-dom').modal('hide');
			deleteFile(id);
		}
	);
}
