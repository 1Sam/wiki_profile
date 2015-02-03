<?php
/* Copyright (C) 1Sam Online <http://1sam.kr> */

if(!defined("__XE__")) exit();

//$__Context = &GLOBALS('__Context__');

/**
  * @wiki_profile_insert.addon.php
  * @author 1Sam (csh@korea.com)
  * @brief 1SamOnline 위키 인명정보 자동입력 애드온
**/

// return unless before_display_content
//if($called_position != "before_display_content" || Context::get('act') == 'dispBoardContentView' || Context::getResponseMethod() != 'HTML' || isCrawler()) return;

//if($called_position == "before_update_Document"
//트리거를 추가하여 자동으로 변수가 저장되록 함

//글을 수정할 때에 위키 애드온에 필요한 변수값을 입력할 수 있는 컴포넌트 자동 추가
//아직 개발중임
if(Context::get('act')=='dispBoardWrite_미완성') {

	//require_once('./addons/wiki_profile/wiki_extravars.php');

	//==========================================
	//확장변수 임의 저장
	//db_document_extra_vars에 6개항목이 저장됨
	//db_document_extra_vars
	// module_srl  document_srl  var_idx  lang_code  value  eid
	// 2362        69128         2         ko        ja     language 
	//==========================================
	
	$wiki_extravars =
'<div class="extra paddingb10">
		<label>검색언어</label>
		<ul>
<li><input name="extra_vars4" class="radio" id="extra_vars2-1002" type="radio" value="ko"><label for="extra_vars2-1002">ko</label></li>
<li><input name="extra_vars4" class="radio" id="extra_vars2-1003" type="radio" value="ja"><label for="extra_vars2-1003">ja</label></li>
<li><input name="extra_vars4" class="radio" id="extra_vars2-1004" type="radio" value="en"><label for="extra_vars2-1004">en</label></li>
<li><input name="extra_vars4" class="radio" id="extra_vars2-1005" type="radio" value="auto"><label for="extra_vars2-1005">auto</label></li>
</ul>
<p>위키페디아의 검색 대상 언어를 선택하세요.</p>		<div class="clearfix"></div>
	</div>';

	$wiki_extravars_js =
	'<script>//<![CDATA[
(function($){
validator.cast("ADD_MESSAGE", ["extra_vars4","임의"]);
})(jQuery);
//]]></script>';
	$wiki_extravars .= $wiki_extravars_js;

		//$temp_output = preg_replace('!<(div)\>(.*?)itemprop(.*?)\<\/(h1)\>!is',
	//$temp_output = preg_replace('!<(.*)"xpress-editor[^\>]*>(.*)>!is','hi',  $output);
	
	//preg_match('!(<.*?)[^\>]xpress-editor(.*?)>!is', $output, $match);




	//미완성이지만 원하는 태그를 찾아 줌
	//클래스명을 이용함
	$class_name = "xpress-editor";
	preg_match("/[^\<]+".$class_name.".*?[^\>]+\>/is", $output, $match);
	$tag_head = '<'.$match[0];
	//'<pre>'.print_r($match,true).'</pre>';
	
	//$temp_output = $tag_head;

	//$temp_output = $match[0];
	
	
	//$added = preg_replace("/[^\<]+".$class_name.".*?[^\>]+\>/is", "<", $output);

	$temp_output = str_replace($tag_head, $wiki_extravars.$tag_head, $output);
	
	//$temp_output = $added;



	
	//preg_match('<div.*xpress\-editor.*([^\>]*)>!is',$output, $match);
	//$temp_output = '<pre>'.print_r($match,true).'</pre>';

	//return $temp_output;
	//}
	//$temp_output .= $result_html
	//echo '<pre>'.print_r($match,true).'</pre>';
	//$match[0] ='';
	
	//$temp_output = '<pre>'.print_r($match,true).'</pre>';

	if($temp_output) {
		$output = $temp_output;
	}
	unset($temp_output);


} else {
	


	// 게시판 내용이 표시될 때 dispBoardContentView
	if(Context::get('document_srl') > 0){
	
		//	if($called_position == 'after_module_proc') {//before_display_content') {//
	
		// 검색어에 붙을 분류의 언어 파일
		Context::loadLang('./addons/wiki_profile/lang');
	
		require_once('./addons/wiki_profile/wiki_profile.lib.php');
	
		Context::addCSSFile('./addons/wiki_profile/css/addon.css');
		Context::addJsFile('.//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js',true,'',-100008);
		Context::addJsFile('./addons/wiki_profile/wiki_profile.js');
			
		//함수에 전역변수로 값을 보내주기 위해 사용하였음
		Context::set('wiki_addon_info', $addon_info);
	
		//$output = $alarm_msg;
		//return;
	
		//require_once('../wiki_profile_insert/addons/wiki_profile_insert/wiki_profile_insert.lib.php');
		// 제목 아래에 표시 .page-header
		// 자바 스크립트로 치환하는 방식
		/*$footer_content = '
		<script type="text/javascript">
		jQuery(document).ready(function($){
		jQuery(".page-header").after(\''.$stickter_list_html.'\');
		});
		</script>';*/
	
		/*
		타이틀에 해당하는 부분의 뒤에 위키페디아 인물정보 내용을 덧붙이는 방식임
		<h3 class="page-header">정려원</h3>
		*/
	
		//함수 정의
		//$wiki_contents = 'wiki_contents';
	
		//if($addon_info->torrent_tag == 'all') {
		//타이들에 해당하는 부분으로 최종 output 값을 구함
		
		//각 게시판의 타이틀 표시 모양에 맞게 수정
		if($addon_info->board_layout == 'simple_strap') {
			/*
				<h1>
					<a href="http://fcrare.inour.net/21663" itemprop="name">정려원</a>
				</h1>
			*/
			$temp_output = preg_replace_callback('!<(h1)\>(.*?)itemprop(.*?)\<\/(h1)\>!is', 'get_wiki_contents', $output);		
		} else {
			$temp_output = preg_replace_callback('!<(h3)([^\>]*)>([^\>]*)\<\/(h3)\>!is', 'get_wiki_contents', $output);
			
			//제목줄 모든 테크 무시?
			//$temp_output = preg_replace_callback('!<(h3)([^\>]*)>(\<[^\>]*\>)\<\/(h3)\>!is', 'get_wiki_contents', $output);
	
		}
		
		//preg_match_all('!<(h3)([^\>]*)>([^\>]*)\<\/(h3)\>!is', $output, $match);
	
		//$temp_output = '<pre>'.print_r($match,true).'</pre>';
	
		//return $temp_output;
		//}
		//$temp_output = $stickter_list_html;
	
		if($temp_output) {
			$output = $temp_output;
		}
		unset($temp_output);
	
	//Context::setResponseMethod($method='HTML');
	//Context::addHtmlHeader('sssssssssssss');
				//Context::addHtmlFooter($footer_content);
	}
}
