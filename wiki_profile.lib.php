<?php
if(!defined('__ZBXE__')) exit();
if(!defined('__XE__')) exit();

function get_wiki_contents($inputs) {
	$document_srl = Context::get('document_srl');
   	//$addon_path = getUrl('').'addons/wiki_profile_insert/';

	//전역변수 받아 오기
	$wiki_addon_info = Context::get('wiki_addon_info');

	/*
       $header_content = '';
       	$header_content .= '
          <script type="text/javascript">
           function insertSticker(sticker_src) {
             // html 모드
               
               get_by_id("page-header).value += text.join("");
            }
         </script>
        ';
       Context::addHtmlheader($header_content);
	*/

// http://ko.wikipedia.org/w/api.php?action=mobileview&page=정은지_(가수)&sections=0&prop=text


	//키워드 생성
	$keyword = get_keyword($document_srl); // 4가지요소 포함
	//echo print_r($keyword,true);
	//echo $keyword['title'];
	//return;

	/**
	/// wikidata 정보
	**/
	if($wiki_addon_info->languages !== 'off') {

		// 한국위키가 포함된 경우 $wiki_data['title_ko']값이 포함됨
		$wiki_data = get_wikidata($keyword);
		//$result_html .= $wiki_data['html'];
		//return $wiki_data['html'];
		//return $wiki_data['title_ko'];
	}


	//echo $keyword['title_ko'];

	// 한국위키 있다면 wikidata.org 에서 받아온 한국위키 검색어 사용
	if($wiki_data["title_ko"] && $wiki_addon_info->kowiki == '1') { 

		//$keyword['title_encoded'] .= $keyword['job'] !== '미분류' && $keyword['job'] ? "_(".keyword2hex($keyword['job']).")": '';

		$wiki_site = "http://ko.wikipedia.org";
		$wiki_options = "/w/api.php?action=mobileview&sections=0&prop=text&format=json&page=".keyword2hex($wiki_data['title_ko']);

		$result = get_json2array($wiki_site, $wiki_options);
		$resultx = $result['mobileview']['sections'][0]['text'];
		
		//위키피디아의 테이블 형태를 알맞게 수정하기
		$resultx = str_replace('<p></p>', '',$resultx);	
		$resultx = preg_replace("/ style=(\"|\')?([^\"\']+)(\"|\')?/","",$resultx);

		//$resultx = preg_replace("/[^\_]infobox/is","info table table-striped", $resultx);
		$resultx = str_replace('"infobox', '"infobox table table-striped',$resultx);

		//$resultx = str_replace('<table', '<div class="panel-body"><table',$resultx);
		//$resultx = str_replace('/table>', '/table></div>',$resultx);

		$resultx = str_replace('<p>', '<div class="panel-footer">',$resultx);
		$resultx = str_replace('</p>', '</div>',$resultx);
		$resultx = str_replace('"/wiki/','"http://'.$keyword['lang'].'.wikipedia.org/wiki/',$resultx);

	//애드온 옵션에서 한국위키 자동사용을 선택하지 않은 경우, ko, en, ja 그대로 사용
	} else {

		$wiki_site = "http://".$keyword['lang'].".wikipedia.org";
		$wiki_options = "/w/api.php?action=mobileview&sections=0&prop=text&format=json&page=".$keyword['title_encoded'];

		$result = get_json2array($wiki_site, $wiki_options);
		$resultx = $result['mobileview']['sections'][0]['text'];

		
		//위키피디아의 테이블 형태를 알맞게 수정하기
		$resultx = str_replace('<p></p>', '',$resultx);
		$resultx = preg_replace("/ style=(\"|\')?([^\"\']+)(\"|\')?/","",$resultx); 
		
		//$resultx = preg_replace("/[^\_]infobox/is","info table table-striped", $resultx);
		$resultx = str_replace('"infobox', '"infobox table table-striped',$resultx);
		
		//$resultx = str_replace('<span', '<li',$resultx);
		//$resultx = str_replace('/span>', '/li>',$resultx);

		//$resultx = str_replace('<table', '<div class="panel-body"><table',$resultx);
		//$resultx = str_replace('/table>', '/table></div>',$resultx);

		$resultx = str_replace('<p>', '<div class="panel-footer">',$resultx);
		$resultx = str_replace('</p>', '</div>',$resultx);
		$resultx = str_replace('"/wiki/','"http://'.$keyword['lang'].'.wikipedia.org/wiki/',$resultx);

	}


	//키워드 뒤에 붙을 분류
	/*
	elseif($keyword['lang'] == 'ko') {

		//$keyword['title_encoded'] .= $keyword['job'] !== '미분류' && $keyword['job'] ? "_(".keyword2hex($keyword['job']).")": '';

		$wiki_site = "http://".$keyword['lang'].".wikipedia.org";
		$wiki_options = "/w/api.php?action=mobileview&sections=0&prop=text&format=json&page=".$keyword['title_encoded'];

		$result = get_json2array($wiki_site, $wiki_options);
		$resultx = $result['mobileview']['sections'][0]['text'];

		
		//위키피디아의 테이블 형태를 알맞게 수정하기
		$resultx = str_replace('<p></p>', '',$resultx);	
		
		$resultx = str_replace('infobox', 'infobox table table-striped',$resultx);
		$resultx = str_replace('<table', '<div class="panel-body"><table',$resultx);
		$resultx = str_replace('/table>', '/table></div>',$resultx);

		$resultx = str_replace('<p>', '<div class="panel-footer">',$resultx);
		$resultx = str_replace('</p>', '</div>',$resultx);


		if(!$resultx) {
			$wiki_options = "/w/api.php?action=mobileview&sections=0&prop=text&format=xml&page=".$keyword->title;
			$result = getHtml($wiki_site, $wiki_options, $keyword['lang']);
			$keyword['title_encoded'] = $keyword_origin;
		}

	} elseif($keyword['lang'] == 'ja') {
		//$keyword = str_replace(' ', '_', $keyword);
		//$keyword_origin = $keyword;

		$wiki_site = "http://".$keyword['lang'].".wikipedia.org";
		$wiki_options = "/w/api.php?action=mobileview&sections=0&prop=text&format=json&page=".$keyword['title_encoded'];

		$result = get_json2array($wiki_site, $wiki_options);
		$resultx = $result['mobileview']['sections'][0]['text'];

		
		//위키피디아의 테이블 형태를 알맞게 수정하기
		$resultx = str_replace('<p></p>', '',$resultx);	
		
		$resultx = str_replace('infobox', 'infobox table table-striped',$resultx);
		$resultx = str_replace('<table', '<div class="panel-body"><table',$resultx);
		$resultx = str_replace('/table>', '/table></div>',$resultx);

		$resultx = str_replace('<p>', '<div class="panel-footer">',$resultx);
		$resultx = str_replace('</p>', '</div>',$resultx);



		if(!$resultx) {
		$wiki_options = "/w/api.php?action=mobileview&sections=0&prop=text&format=xml&page=".$keyword_origin;
		$resultx = getHtml($wiki_site, $wiki_options, $keyword['lang']);
		$keyword = $keyword_origin;
		}

	} elseif($keyword['lang'] == 'en') {
		//$keyword = str_replace(' ', '_', $keyword);
		//$keyword_origin = $keyword;
		//$keyword .= $job !== '미분류' && $job ? "_(".$job.")": '';
		$wiki_site = "http://".$keyword['lang'].".wikipedia.org";
		$wiki_options = "/w/api.php?action=mobileview&sections=0&prop=text&format=json&page=".$keyword['title_encoded'];

		$result = get_json2array($wiki_site, $wiki_options);
		$resultx = $result['mobileview']['sections'][0]['text'];

		
		//위키피디아의 테이블 형태를 알맞게 수정하기
		$resultx = str_replace('<p></p>', '',$resultx);	
		
		$resultx = str_replace('infobox', 'infobox table table-striped',$resultx);
		$resultx = str_replace('<table', '<div class="panel-body"><table',$resultx);
		$resultx = str_replace('/table>', '/table></div>',$resultx);

		$resultx = str_replace('<p>', '<div class="panel-footer">',$resultx);
		$resultx = str_replace('</p>', '</div>',$resultx);

		//echo print_r($result,true);

		if(!$resultx) {
		$wiki_options = "/w/api.php?action=mobileview&sections=0&prop=text&format=xml&page=".$keyword_origin;
		$result = getHtml($wiki_site, $wiki_options, $keyword['lang']);
		$resultx = $result['mobileview']['sections'][0]['text'];
		$keyword = $keyword_origin;
		}
		//wikidata 내용이 없으면, 성공한 검색어의 redirected 를 참조하여 wikidata를 다시 구함
		if($resultx && !$wiki_data['html']) {
			$keyword['title_encoded'] = keyword2hex($result['mobileview']['redirected']);
			$wiki_data['html'] = get_wikidata($keyword);
		}
	} 
	*/



	//imdb에서 검색함
	if(!$resultx) { //return $inputs[0].'<span class="label label-danger">wiki에 인물정보 없음</span>';
		$keyword_origin = $documentx->variables[title];

		//http://www.imdb.com/xml/find?json=1&nr=1&nm=on&q=Jemma Dallender
		$wiki_site = 'http://www.imdb.com';
		$wiki_options = "/xml/find?json=1&nr=1&nm=on&q=".$keyword_origin;
		//"/w/api.php?action=mobileview&sections=0&prop=text&format=xml&page=".$keyword_origin;
		$resultx = getHtml_imdb($wiki_site, $wiki_options);
		
		$imdb_id = searcharray($keyword_origin, 'name', $resultx);
		
		// imdb 영화배우 프로필
		//http://www.imdb.com/name/nm4728487/resume?ref_=nm_ov_res
		$imdb_site = "http://www.imdb.com";
		$imdb_options = "/name/".$imdb_id. "/resume?ref_=nm_ov_res";
		$imdb_url = "http://www.imdb.com/name/".$imdb_id. "/resume?ref_=nm_ov_res";
		
		//잠정보류 정규식 때문임
		//$resultx = getHtml_imdb_resume($imdb_site, $imdb_options);
		$resultx ='<a href="'.$imdb_url.'" class="btn btn-primary btn-xs" target="_blank">imdb Resume</a>';
		
		//$key = array_search('Jemma Dallender', $resultx, true);
		
		//$resultx = print_r($resultx[name_exact][1][name],true). print_r($key,true).'//'.print_r($resultx,true);  
		
		$keyword = $keyword_origin;
	}

	/*if($resultx == NULL) {

		$wiki_site = "http://".$keyword['lang'].".wikipedia.org";
		$wiki_options = "/w/api.php?action=mobileview&sections=0&prop=text&format=xml&page=".$keyword;
		
		
		$resultx = getHtml($wiki_site, $wiki_options, $keyword['lang']);
	}*/



	//
	// 결과값 정리 *****************************************************************
	//

	// 컨텐츠의 제목 아래에 포함될 html 내용
	// 설명 문구
	if($wiki_addon_info->alarm_msg_visual == 'on') {

		//$alarm_msg = print_r(explode($alram_msg, '.', '<br>'));
		// 줄나누기
		$wiki_addon_info->alarm_msg = str_replace('.', '.<br>', $wiki_addon_info->alarm_msg);

		//맨위에 표시될 주의 문구 내용
		$result_html = '
<div class="panel panel-info">
  <div class="panel-heading">
	<h5 class="panel-title">
  	<a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
		위키페디아 인물정보 자동입력 애드온 <span class="glyphicon glyphicon-question-sign"></span>
	</a>
	</h5>
  </div>

  <div id="collapseOne" class="panel-collapse collapse">
  <div class="panel-body ">
  	wikipedia API를 통해 인물정보를 자동으로 게시물에 삽입합니다. wikipedia에서 json형태의 값을 받아오며 "htmlspecialchars_decode()"로 문자를 변경하고, 검색어는 "bin2hex()"로 헥사코드로 변경하여 특수 알파벳도 사용할 수 있도록 하였습니다.
  </div>
  </div>
  <ul class="list-group">
    <li class="list-group-item">'.$wiki_addon_info->alarm_msg.'</li>
  </ul>
</div>';
	}
	
	// 위키페디아 인물정보 아웃 박스 시작
    //$result_html .= print_r($addon_info,true);
	$result_html .= $wiki_data['html']; //navbar로 표현된 각국 위키링크

    $result_html .= '<div class="wiki_profile_insert panel panel-default">';


	//약간 변형
	$resultx = str_replace('td colspan="2"', 'td rowspan="20"',$resultx);
	//$resultx = preg_replace('/=\"([0-9])\"/s', '100',$resultx);
	//$resultx = preg_replace('/th.*[^>]colspan=\"[0-9]\"/s', 'th colspan="3"',$resultx);
	//$resultx = str_replace('th colspan="2"','th colspan="3"',$resultx);
	

	$result_html .= $resultx;

	//$result_html .= web_search_getcontent2(1,2,3,4);
       	//$result_html .= $job.$keyword['lang'].$keyword.'<br>'.$wiki_site.$wiki_options.'</ul></div>';
	$result_html .= '</div>';


	//관리자에게만 출력하는 별도 내용
	$logged_info = Context::get('logged_info');
	if($logged_info->is_admin) {
		$result_html .= '<ul class="list-group">';
		$result_html .= '<li class="list-group-item list-group-item-success">'.$wiki_site.$wiki_options.'</li>';
		$result_html .= '<li class="list-group-item list-group-item-info">'.print_r($keyword,true).'</li>';
		$result_html .= '</ul>';
	}

	// 특수문자 슬래쉬를 제거함 stripslashes
	//return print_r($inputs,true).$keyword.stripslashes($result_html);
	return $inputs[0].$result_html;
}



// 글 제목
// 검색 키워드가 됨
function get_keyword($document_srl) {

	// $document_srl = Context::get('document_srl'); 

	$oDocumentModel = getModel('document');
	$document = $oDocumentModel->getDocument($document_srl);

	$title = $document->variables[title];

	//게시글 저장시에 사용자 변수값(job,language)를 불러온다.
	//가수,배우 ; ko,ja,en;
	
	/*
	$oModuleModel = getModel('module');
	$midx = Context::get('mid');
	$thisx = $oModuleModel->getModuleInfoByMid($midx);
	$module_srl = $thisx->module_srl;
	*/
	$module_srl = Context ::get('module_srl'); 
	$extravars = $oDocumentModel->getExtraVars($module_srl, $document_srl);
	


	//enwiki, kowiki, jawiki 등 검색을 위해 위키페디아 서브도메인 주소로 사용됨
	$lang = $extravars[2]->value; //ko

	/*// 구체적인 카테고리 언어 배열
	$lan = array(
		'en'=>array('singer'=>'singer','actress'=>'actress','actor'=>'actor','comedian'=>'comedian','none_selected'=>'none')

		,'ko'=>array('singer'=>'가수','actress'=>'배우','actor'=>'남자 배우','comedian'=>'희극인','none_selected'=>'미분류')

		,'ja'=>array('singer'=>'가수','actress'=>'배우','actor'=>'남자 배우','comedian'=>'희극인','none_selected'=>'미분류')
	);
	*/
	

	
	
	//$year_month = $extravarsx[3]->value; //1990년8월


    /**
     * @brief 한글이 들어간 url의 decode
     **/
	//$keyword = url_decode($documentx->variables[title]);

	//( 괄호문자로 일본어 발음 표기 가능하게 함
	// 伊東美咲(이토 미사키) - 전차남(드라마판)
	$titles = explode('(', $title);

	//앞뒤 공백제거
	$title = trim($titles[0]);

	//**
	//** 위키페디아 언어선택값 없을 때
	//** 자동으로 언어값 지정
	//**
	
	//** 영어일 때
	if(preg_match("/[a-zA-Z]/",$title)) {

		$lang = 'en';

		//if($job !== '미분류' && $job) $job = 'actress';

		$title = str_replace(' ', '_', $title);

		//모든 공백제거
		$title = preg_replace("/\s+/", "", $title);
		//헥사코드로 키워드 변경
		$title_encoded = keyword2hex($title);
		//검색 분류항목 조합하기

	// 일본어
	} elseif (preg_match_all('!['
		.'\x{3040}-\x{309F}'// 히라가나
		.'\x{30A0}-\x{30FF}'// 가타카나
		.'\x{31F0}-\x{31FF}'// 가타카나 음성 확장
	    .'\x{2E80}-\x{2EFF}'// 한,중,일 부수 보충
	    .'\x{31C0}-\x{31EF}\x{3200}-\x{32FF}'
	    .'\x{3400}-\x{4DBF}\x{4E00}-\x{9FBF}\x{F900}-\x{FAFF}'
	    .'\x{20000}-\x{2A6DF}\x{2F800}-\x{2FA1F}'// 한,중,일 호환한자
	    .']+!u', $title, $match)) {

		$lang = 'ja';

		//모든 공백제거
		$title = preg_replace("/\s+/", "", $title);
		//헥사코드로 키워드 변경
		$title_encoded = keyword2hex($title);
		//검색 분류항목 조합하기

	// 한국어
	} else {
		$lang = 'ko';

		//모든 공백제거
		// 일본이름 '후지타 사유리'같은 경우를 위해... 공백제거는 하지 않기로 함
		//트림으로 바꾸고 '_'삽입
		//$title = preg_replace("/\s+/", "", $title);
		$title = trim($title);
		$title = str_replace(' ', '_', $title);
		//헥사코드로 키워드 변경
		$title_encoded = keyword2hex($title);
		//검색 분류항목 조합하기
	}

	//간단한 카테고리 언어 배열
	$lang_arr = array(
		'가수'=>array('en'=>'singer','ko'=>'가수','ja'=>'가수')
		,'배우'=>array('en'=>'actress','ko'=>'배우','ja'=>'배우')
		,'남자 배우'=>array('en'=>'actor','ko'=>'남자 배우','ja'=>'남자 배우')
		,'희극인'=>array('en'=>'comedian','ko'=>'희극인','ja'=>'희극인')
		,'미분류'=>array('en'=>'none','ko'=>'미분류','ja'=>'미분류')
	);

	// 분류값이 카테고리 추가
	//검색 보조 구분자 (가수) (배우) (1990년8월)

	//$job = $extravars[3]->value ? $extravars[3]->value : $extravars[1]->value; //가수
	//$title_encoded .= ($job !== '미분류' && $job ? "_(".keyword2hex($lang_arr[$job][$lang]).")": '');

	
	if($extravars[3]->value) {
		$job = $extravars[3]->value;
		$job = str_replace(' ', '_', $job);
		$title_encoded .= '_('.$job.')';
	} elseif($extravars[1]->value &&  $extravars[1]->value !== '미분류') {
		$job = $extravars[1]->value;
		$title_encoded .= '_('.$job.')';
	}

	
	//echo '*'.print_r($lang_arr,true).'<br><br><br>'.$lang.'//'.$job.'//'.$lang_arr[$job][$lang];
	//echo '<br><br><br>'.$title_encoded;

	//echo $title_encoded;
	/*
	//영어는 각 단어의 첫글자를 대문자로 변환해야함
	$keyword = strtolower($keyword);
	$keyword = ucwords($keyword);
	//$keyword = urlencode($keyword);
	//$keyword = fnUnicodeToUTF8($keyword);
	*/	

	$keyword = array(
		"title" => $title // 인코딩되지 않은 검색어
		,"title_encoded" => $title_encoded // 인코딩된 검색어
		,"lang" => $lang // en, ko, jp
		,"job" => $job // _(배우) _(가수) _(1998년9월)
		//,"title_ko" =>''
	);

	return $keyword;
}


// 키워드를 %가 붙은 16진수 HEX 코드로 변경
//  keyword2hex($keyword)
function keyword2hex($keyword) {
	$keyword = bin2hex($keyword);
	$keyword = chunk_split($keyword, 2, '%');
	$keyword = '%' . substr($keyword, 0, strlen($keyword) - 1);
	// 대문자로 변경
	$keyword = strtoupper($keyword);
	
	return $keyword;
}

function getHtml_imdb_resume($site, $url) {
	
	$curl_handle = curl_init();
	curl_setopt($curl_handle, CURLOPT_URL, $site.$url);
	curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
	curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl_handle, CURLOPT_USERAGENT, $site);
	//$json = curl_exec($curl_handle);
	$json = curl_exec($curl_handle);

	//json을 Obect Array로 변환
	//$json = json_decode($json);
	
	// Function to Convert stdClass Objects to Multidimensional Arrays
	//$json = objectToArray($json);
	//preg_match_all('/<div.*class="resume_box".*>(.*)<\/div>*<\/div>/isU', $json, $match);
	//preg_match_all('~<div\s*class=\"resume_box\"*>\s*(<div.*?</div>\s*)?(.*?)</div>~is', $json, $match );
	
	//$temp_output = preg_replace_callback('!<(h3)([^\>]*)>([^\>]*)\<\/(h3)\>!is', preg_match_by_class, $output);

	preg_match('!<div[^\>]*\>(.*?)\<\/div\>!is', $json, $match); 
	//preg_match("~<div[^>]*class=\"resume\_box\".*?\>([^`]*?)<\/div>~is", $json, $match);
	//preg_match_all('/<div [^\>]* class=\"resume\_box\"*[^\>](.*?)<\/div>/is',$json,$match);
	//$match = preg_match_all('~<div class="(\w*)">~', json, $matches);
	//$value=preg_match_all('/<div class=\"resume\_box\"*[^\>](.*?)<\/div>/s',$json,$match);
	//preg_match('!<div [^\>]* class\=\"resume\_box\"*\>([^\>]*)\<\/div\>!is', $json, $match);
	
	//$match[0] ='';
	//$match = $value;
	curl_close($curl_handle);

	//불필요한 부분 앞과 뒤를 정리함
	//$json = str_replace('/**/({"mobileview":','',$json);
	//$json = str_replace("})",'',$json);
	
	//preg_match('!<(section)([^\>]*)>([^\>]*)\<\/(section)\>!is', $xml, $match);
	
	//$result=$match[0];
	
	//xml로 받은 특수문자 태그를 일반 html 태그로 변환
	//$result = htmlspecialchars_decode($match[0]);//print_r($match,true);
	//htmlspecialchars_decode() 

	//속성 변경
	//$result = str_replace('"infobox','"infobox table table-striped',$result);
	//링크 변경
	

	
	return '<pre>'.print_r($match, true).'</pre>';
}

// json 으로 반환
// getHtml_imdb()
function getHtml_imdb($site, $url) {
	
	$curl_handle = curl_init();
	curl_setopt($curl_handle, CURLOPT_URL, $site.$url);
	curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
	curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl_handle, CURLOPT_USERAGENT, $site);
	//$json = curl_exec($curl_handle);
	$json = curl_exec($curl_handle);

	//json을 Obect Array로 변환
	$json = json_decode($json);
	
	// Function to Convert stdClass Objects to Multidimensional Arrays
	$json = objectToArray($json);
	
	curl_close($curl_handle);

	//불필요한 부분 앞과 뒤를 정리함
	//$json = str_replace('/**/({"mobileview":','',$json);
	//$json = str_replace("})",'',$json);
	
	//preg_match('!<(section)([^\>]*)>([^\>]*)\<\/(section)\>!is', $xml, $match);
	
	//$result=$match[0];
	
	//xml로 받은 특수문자 태그를 일반 html 태그로 변환
	//$result = htmlspecialchars_decode($match[0]);//print_r($match,true);
	//htmlspecialchars_decode() 

	//속성 변경
	//$result = str_replace('"infobox','"infobox table table-striped',$result);
	//링크 변경
	return $json;
}


function get_wikidata($keyword) {
	// 위키페디아 언어별 검색 기능
	// 제목에 기입된 이름으로 일본어 및 다른 언어에서도 검색이 가능하도록 함
	// http://www.wikidata.org/w/api.php?action=wbgetentities&sites=enwiki&languages=en|ja|ko|cs|de|es|fr|it|pl|pt|ru&props=labels&format=xml&titles=Gabriella Wilde

	/*
	// json으로 국가별 검색 키워드 반환
	*/

		/*
		$wikidata_site = "http://www.wikidata.org";
		
		if($wiki_addon_info->languages == 'g11') {
			$wiki_lang_options = "/w/api.php?action=wbgetentities&sites=".$keyword['lang']."wiki&languages=ko|en|ja|zh|cs|de|es|fr|it|pl|pt|ru&props=labels&format=json&titles=".$keyword;
		} else {
			$wiki_lang_options = "/w/api.php?action=wbgetentities&sites=".$keyword['lang']."wiki&props=labels&format=json&titles=".$keyword;
		}
		
		
		$lang_result = getHtml_imdb($wikidata_site, $wiki_lang_options);
		$first = $lang_result['entities'];
		
		//배열의 첫번째 키값
		reset($first);
		//echo key($first);
		$wiki_Q_id = key($first);

		// 국가순으로만 된 배열
		//print_r($first[$wiki_Q_id]['labels'],true)
		
		//$select = '<div class="btn-group">';
		$select = '<div class="navbar-xs"><div class="navbar-primary"><nav class="navbar navbar-inverse" role="navigation"><ul class="nav navbar-nav">';

		foreach($first[$wiki_Q_id]['labels'] as $key => $value) {
		//echo $key." /".$value['value']."<br />";
		//<span><a class="btn" src="">en</a></span>
			//$select .= '<a type="button" class="btn btn-defaul-xs" href="http://'.$key.'.wikipedia.org/wiki/'.$value["value"].'">'.$key.'</a>';
			if($keyword['lang'] == $key) {
	        	$select .= '<li class="active"><a href="http://'.$key.'.wikipedia.org/wiki/'.$value["value"].'" data-toggle="tooltip" title="'.$value["value"].'">'.$key.'</a></li>';
			} else {
		        $select .= '<li><a class="tool" href="http://'.$key.'.wikipedia.org/wiki/'.$value["value"].'" data-toggle="tooltip" title="'.$value["value"].'">'.$key.'</a></li>';
			}
	
			//foreach($value as $keys => $values){
			//echo $key." /" .$keys." / ".$values."<br />";
			//}
		}
		//$select .= '</div>';
		//$select .= '<div class="btn-group btn-group-xs">...</div>';

		$select .= '</ul></nav></div></div>';
		//$select .= '<div class="btn-group btn-group-xs"><button type="button" class="btn btn-inverse">1</button><button type="button" class="btn btn-inverse">2</button></div>';


		//return $select;
		/*
		// xml로 국가별 검색 키워드 반환

	
		$wikidata_site = "http://www.wikidata.org";
		$wiki_lang_options = "/w/api.php?action=wbgetentities&sites=".$keyword['lang']."wiki&languages=en|ja|ko|cs|de|es|fr|it|pl|pt|ru&props=labels&format=xml&titles=".$keyword;

		$lang_result = getHtml_xml($wikidata_site, $wiki_lang_options);
		*/
		
		//return  '<pre>***'.$keyword.'<br>'.$keyword_origin.'<br>'.print_r($lang_result,true).'</pre>';
		

		$result = array();
		
		$wikidata_site = "http://www.wikidata.org";

		if($wiki_addon_info->languages == 'g11') {
			$wiki_lang_options = "/w/api.php?action=wbgetentities&sites=".$keyword['lang']."wiki&languages=ko|en|ja|zh|cs|de|es|fr|it|pl|pt|ru&props=labels&format=json&titles=".$keyword['title_encoded'];
		} else {
			$wiki_lang_options = "/w/api.php?action=wbgetentities&sites=".$keyword['lang']."wiki&props=sitelinks&format=json&titles=".$keyword['title_encoded'];
		}
	
		$lang_result = get_json2array($wikidata_site, $wiki_lang_options);
		//return print_r($lang_result,true);

		//결과값이 있을 때만 출력
		if(!$lang_result['entities'][-1]) {

			//배열의 첫번째 키값
			$first = $lang_result['entities'];
			reset($first);

			//Q code 구하기
			//echo key($first);
			$wiki_Q_id = key($first);

			// 국가별 검색사이트만으로 된 배열
			//return print_r($first[$wiki_Q_id]['sitelinks'],true);
		
			//$select = '<div class="btn-group">';
			//wikidata의 내용을 nav 바로 표현
			$result['html'] = '<div class="navbar"><div class="navbar-primary"><nav class="navbar navbar-inverse" role="navigation">
  <div class="container-fluid">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
	  <!--//Q코드 삽입-->
	  <a class="navbar-brand" href="https://www.wikidata.org/wiki/'.$wiki_Q_id.'?uselang=ko" data-toggle="tooltip" title="'.$wiki_Q_id.'" target="_blank">Wikidata.org</a>
    </div>		
		
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
<ul class="nav navbar-nav">';
			foreach($first[$wiki_Q_id]['sitelinks'] as $key => $value) {
				//echo $key." /".$value['title']."<br />";
				//<span><a class="btn" src="">en</a></span>
				//$select .= '<a type="button" class="btn btn-defaul-xs" href="http://'.$key.'.wikipedia.org/wiki/'.$value["value"].'">'.$key.'</a>';
				$key = str_replace('wiki','',$key);
				$key = str_replace('_','-',$key);

				// 좀더 생각해봐야함
				if($keyword['lang'] == $key) {
	        		$result['html'] .= '<li class="active"><a href="http://'.$key.'.wikipedia.org/wiki/'.$value["title"].'?uselang=ko" data-toggle="tooltip" title="'.$value["title"].'" target="_blank">'.$key.'</a></li>';
				} else {
		    	    $result['html'] .= '<li><a class="tool" href="http://'.$key.'.wikipedia.org/wiki/'.$value["title"].'?uselang=ko" data-toggle="tooltip" title="'.$value["title"].'" target="_blank">'.$key.'</a></li>';
				}
				
				//한국어가 포함되어 있는지 검사하고 참이면 한국위키
				if($key == 'ko') $result['title_ko'] = $value["title"];
			
				//한국어가 포함되어 있는지 검사하고 참이면 한국위키
				//if($key == 'ko') $keyword['title_ko'] = $value["title"];
	
				//foreach($value as $keys => $values){
				//echo $key." /" .$keys." / ".$values."<br />";
				//}
			}
			//$select .= '</div>';
			//$select .= '<div class="btn-group btn-group-xs">...</div>';
			
			
			$result['html'] .= '</ul></div></div></nav></div></div>';
			//$select .= '<div class="btn-group btn-group-xs"><button type="button" class="btn btn-inverse">1</button><button type="button" class="btn btn-inverse">2</button></div>';
			//$result_html .= $select;
			//$result_html .= $lang_result;
			
			
			// html 코드와 한국어 검색어를 리턴해줌
			return $result;
		}
}



 
// xml로 반환 - 버그
// getHtml_xml()
function getHtml_xml($site, $url) {
	
	$curl_handle = curl_init();
	curl_setopt($curl_handle, CURLOPT_URL, $site.$url);
	curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
	curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl_handle, CURLOPT_USERAGENT, $site);
	$xml = curl_exec($curl_handle);

	//json을 Obect Array로 변환
	$xml = json_decode($xml);	

	curl_close($curl_handle);


	//불필요한 부분 앞과 뒤를 정리함
	//$json = str_replace('/**/({"mobileview":','',$json);
	//$json = str_replace("})",'',$json);

	return $xml;	
	preg_match('!<labels>(.*?)\<\/labels\>!is', $xml, $match);
	
	//$result=$match[0];
	
	//xml로 받은 특수문자 태그를 일반 html 태그로 변환
	//$result = htmlspecialchars_decode($match[0]);//print_r($match,true);
	//htmlspecialchars_decode() 

	//속성 변경
	//$result = str_replace('"infobox','"infobox table table-striped',$result);
	//링크 변경
	return $match;
}


// imdb의 json array에서 검색어($value)로 id값 검출
// searcharray()
function searcharray($value, $key, $array) {
	//2단계구조
	foreach ($array as $k => $val) { //$k 0,1,2,3
		foreach ($val as $m => $val2) { //$val = 0,1,2,3,  $m= id  , $val2 = mn4891387
	   	//return $k;
		if($val2[$key] == $value) return $val2['id'];
	       //if ($val[$key] == $value) return $k;
	   	}
	   	//return $val;
	   	//return $val[$key].'**'. $value;
	   	//if($val[$key] == $value) return $val['id'] ;
	   	//if($array[$k][$key] == $value) return 'ok';
	}
   return NULL;
}



// objectToArray($d)
function objectToArray($d) {
	if (is_object($d)) {
		// Gets the properties of the given object
		// with get_object_vars function
		$d = get_object_vars($d);
	}
 
	if (is_array($d)) {
		/*
		* Return array converted to object
		* Using __FUNCTION__ (Magic constant)
		* for recursive call
		*/
		return array_map(__FUNCTION__, $d);
	}
	else {
		// Return array
		return $d;
	}
}


// function getHtml($site, $url, $languagex)
function getHtml($site, $url, $languagex) {
	
	$curl_handle = curl_init();
	curl_setopt($curl_handle, CURLOPT_URL, $site.$url);
	curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
	curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl_handle, CURLOPT_USERAGENT, $site);
	//$json = curl_exec($curl_handle);
	$xml = curl_exec($curl_handle);
	curl_close($curl_handle);

	//불필요한 부분 앞과 뒤를 정리함
	//$json = str_replace('/**/({"mobileview":','',$json);
	//$json = str_replace("})",'',$json);
	preg_match('!<(section)([^\>]*)>([^\>]*)\<\/(section)\>!is', $xml, $match);
	//$result=$match[0];
	
	//xml로 받은 특수문자 태그를 일반 html 태그로 변환
	$result = htmlspecialchars_decode($match[0]);//print_r($match,true);
	//htmlspecialchars_decode() 

	//속성 변경
	$result = str_replace('"infobox','"infobox table table-striped',$result);
	//링크 변경
	$result = str_replace('"/wiki/','"http://'.$languagex.'.wikipedia.org/wiki/',$result);
	return $result;
	


	/*	
	$json = str_replace('"}]}}','',$json);


	//속성 변경
	$json = str_replace('"infobox','"infobox table table-striped',$json);
	//링크 변경
	$json = str_replace('"/wiki/','"http://'.$languagex.'.wikipedia.org/wiki/',$json);

	//필요부분만 때어냄
	$json = split('"text":"',$json);
	//$obj = json_decode($json);
	*/


	//return print_r($obj,true);

/*
	require_once $_SERVER['DOCUMENT_ROOT'].'/config/config.inc.php';
	$oContext = &Context::getInstance();
	$oContext->init();

	$oDB = &DB::getInstance();
	 //$args->document_srl = 1608;
	 //$args->rateval = 1; //1점이상인 사람만 가져옴

	$member_srl = 4;
	$tablename = 'db_document_voted_log';

	// 사용자가 투표한 적이 있는지 검사
	$result = $oDB->_query("select rateval from $tablename where member_srl='$member_srl' && document_srl='$document_srl'");
	$xxx = $oDB->_fetch($result); //배열 재구성
*/

	//echo print_r($output,true);

	//$oContext->close();

	//print_r()을 이용해서 출력이 불가능하여 변수의 값을 모두 파일로 저장하여 찾아보고 실질값만을 이용함
	//$json[1] .= $documentx ->variables[title];
	
	//$document_srl = Context::get('document_srl'); 

	/* 테스트용
	$oDocumentModel = getModel('document');
	$documentx = $oDocumentModel->getDocument($document_srl,false,'language','');



	$oModuleModel = getModel('module');
	$midx = Context::get('mid');
	$thisx = $oModuleModel->getModuleInfoByMid($midx);
	$module_srl = $thisx->module_srl;
		$extravarsx = $oDocumentModel->getExtraVars($module_srl, $document_srl);

	    $myFile = "testFile15.txt";
    	$fh = fopen($myFile, 'w') or die("can't open file");
	    $stringData = print_r($json[1], true);
    	fwrite($fh, $stringData);
    	fclose($fh);*/


	return $json[1];
}

function fnUnicodeToUTF8($string) {
	return html_entity_decode(preg_replace("/%u([0-9a-f]{3,4})/i","&#x\\1;",urldecode($string)), null, 'UTF-8');
}





/**
/// 사이트 자료 가져오기
/// json -> std Object -> Array 로 반환
**/

function get_json2array($site, $url) {
	
	$curl_handle = curl_init();
	curl_setopt($curl_handle, CURLOPT_URL, $site.$url);
	curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
	curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl_handle, CURLOPT_USERAGENT, $site);

	//$json = curl_exec($curl_handle); 헥사코드 상태
	$json = curl_exec($curl_handle);
	
	//echo print_r($json,true);

	//json을 Obect Array로 변환 및 UTF-8로 변환
	// -> 화살표 형식으로 사용해야함
	$object = json_decode($json);
	//echo '<br><br>'.print_r($object,true);	
	//echo '<br><br>'.print_r($object->entities,true);
	//echo '<br><br>'.print_r($object->entities[0],true); //에러
	//echo '<br><br>'.print_r($object[0],true); //에러


	// Function to Convert stdClass Objects to Multidimensional Arrays
	// Object를 일반 Array로 변환
	// [] 형식으로 사용해야함
	$arr = objectToArray($object);
	//echo '<br><br>'.print_r($arr,true);	
	//echo '<br><br>'.print_r($arr['entities'],true);
	//echo '<br><br>'.print_r($arr['entities'][1],true); //값 안 나옴
	//echo '<br><br>'.print_r($arr[0],true); // 값 안 나옴

	//echo '<br>'.print_r($json,true);	
	curl_close($curl_handle);

	return $arr;
}
