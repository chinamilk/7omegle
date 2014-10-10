
// 提交函数
function submit(e){
	e.preventDefault();
	e.stopPropagation();
	if($("#feed-content").val() || $("#feed-image").val()){
		var formData = new FormData();
		if($("#feed-content").val()){
			formData.append('feed-content', $("#feed-content").val());
			$("#feed-content").val('');
		}
		if($("#feed-image").val()){
			var file = $("#feed-image")[0].files[0];
			if(!file.type.match('image.*')){
				alert('只允许上传 gif|jpg|png 格式的文件');
				return false;
			}
			formData.append('userfile', file, file.name);
			// 图片上传按钮恢复
			$("#feed-image").val('').hide();
			$("#remove-image").hide();
			$("#add-image").show();
		}
		$.ajax({
			type: 'post',
			url: '/i/feed/create',
			dataType: 'json',
			// data: {"together-content": $("#together-content").val(), "csrf": $("#csrfmiddleware").val()},
			data: formData,
			processData: false,
			contentType: false,
			before: function(){
				$(this).attr("disabled", "disabled");
			},
			success: function(json){
				if(json.error == 0){
					window.min_feed_id = json.data.feed.id;
					html = '<div class="well well-large" feed-id="' + json.data.feed.id + '">';
					html += '<div class="cell">';
					html += '<table width="100%" cellspacing="0" cellpadding="0" border="0"';
					html += '<tbody><tr>';
					html += '<td width="50" valign="top" align="center">';
					html += '<img src="' + json.data.user.avatar_small + '" />';
					html += '</td>';
					html += '<td width="10" valign="top"></td>';
					html += '<td width="auto" valign="top" align="left">';
					html += '<a href="/member/' + json.data.user.username + '">' + json.data.user.username + '</a>';
					html += '<div class="sep5"></div>';
					html += '<span class="gray">' + json.data.feed.time + '</span>';
					html += '</td></tr></tbody></table></div>';
					html += '<h5>' + json.data.feed.content + '</h5>';
					if(json.data.feed.image){
						html += '<a href="' + json.data.feed.image + '" target="_blank">';
						html += '<img src="' + json.data.feed.image + '" />';
						html += '</a>';
					}
					html += '</div>';
					$("#feed-container").prepend(html);
				} else {
					alert('error: ' + json.msg);
				}
			},
			error: function(){
				alert('出错了');
			},
			after: function(){
				$(this).attr("disabled", false);
			}
		});
	}
	return false;
}

// 检查动态更新
function check_update(){
	$.ajax({
		type: 'post',
		url: '/i/timeline',
		dataType: 'json',
		data: {"min_feed_id": window.min_feed_id},
		// data: formData,
		success: function(json){
			if(json.error == 0){
				if(json.feed_count > 0){
					window.replace_min_feed_id = json.min_feed_id;
					window.feed_html = json.feed_html;
					if(json.feed_count <= 10){
						document.title = '(' + json.feed_count + ') 7omegle';
						$(".new-feeds-bar-container").text('有 ' + json.feed_count + ' 条新动态').show();
					} else if(json.feed_count > 10){
						document.title = '(10+) 7omegle';
						$(".new-feeds-bar-container").text('有 10+ 条新动态').show();
					}
				}
			}
		}
	});
}

// 获取更新的动态显示在页面中
function get_update(){
	document.title = '7omegle';
	$(this).hide();
	window.min_feed_id = window.replace_min_feed_id;
	$("#feed-container").prepend(window.feed_html);
}

var min_feed_id;  // 客户端最小的 feed id
var replace_min_feed_id;  // 服务器端返回的最小 feed id，用此 id 替换客户端 min_feed_id
var feed_html;  // 服务器端返回的动态更新的 html
$(function(){
	$("#add-image").click(function(e){
		$(this).hide();
		$("#feed-image").click().show();
		$("#remove-image").show();
	});
	$("#remove-image").click(function(e){
		$("#feed-image").val('').hide();
		$(this).hide();
		$("#add-image").show();
	});
	$("#feed-content").keydown(function(e){
		if(e.ctrlKey && e.which === 13){
			$("#feed-create-submit").click();
		}
	});
	// 提交
	$("#feed-create-submit").click(function(e){
		submit(e);
	});

	// 检查动态更新
	window.min_feed_id = $("#feed-container div:first-child").attr('feed-id');
	var check = setInterval(function(){
		check_update();
	}, 30000);

	// 获取更新
	$(".new-feeds-bar-container").click(get_update);
});


