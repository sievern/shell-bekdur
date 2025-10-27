<?php
/** Adminer Editor - Compact database editor
* @link https://www.adminer.org/
* @author Jakub Vrana, https://www.vrana.cz/
* @copyright 2009 Jakub Vrana
* @license https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
* @license https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2 (one or other)
* @version 5.4.1
*/namespace
Adminer;const
VERSION="5.4.1";error_reporting(24575);set_error_handler(function($Hb,$Ib){return!!preg_match('~^Undefined (array key|offset|index)~',$Ib);},E_WARNING|E_NOTICE);$Xb=!preg_match('~^(unsafe_raw)?$~',ini_get("filter.default"));if($Xb||ini_get("filter.default_flags")){foreach(array('_GET','_POST','_COOKIE','_SERVER')as$W){$Jf=filter_input_array(constant("INPUT$W"),FILTER_UNSAFE_RAW);if($Jf)$$W=$Jf;}}if(function_exists("mb_internal_encoding"))mb_internal_encoding("8bit");function
connection($f=null){return($f?:Db::$instance);}function
adminer(){return
Adminer::$instance;}function
driver(){return
Driver::$instance;}function
connect(){$ab=adminer()->credentials();$J=Driver::connect($ab[0],$ab[1],$ab[2]);return(is_object($J)?$J:null);}function
idf_unescape($s){if(!preg_match('~^[`\'"[]~',$s))return$s;$ed=substr($s,-1);return
str_replace($ed.$ed,$ed,substr($s,1,-1));}function
q($P){return
connection()->quote($P);}function
escape_string($W){return
substr(q($W),1,-1);}function
idx($ja,$v,$h=null){return($ja&&array_key_exists($v,$ja)?$ja[$v]:$h);}function
number($W){return
preg_replace('~[^0-9]+~','',$W);}function
number_type(){return'((?<!o)int(?!er)|numeric|real|float|double|decimal|money)';}function
remove_slashes(array$se,$Xb=false){if(function_exists("get_magic_quotes_gpc")&&get_magic_quotes_gpc()){while(list($v,$W)=each($se)){foreach($W
as$Yc=>$V){unset($se[$v][$Yc]);if(is_array($V)){$se[$v][stripslashes($Yc)]=$V;$se[]=&$se[$v][stripslashes($Yc)];}else$se[$v][stripslashes($Yc)]=($Xb?$V:stripslashes($V));}}}}function
bracket_escape($s,$ra=false){static$_f=array(':'=>':1',']'=>':2','['=>':3','"'=>':4');return
strtr($s,($ra?array_flip($_f):$_f));}function
min_version($Vf,$nd="",$f=null){$f=connection($f);$Qe=$f->server_info;if($nd&&preg_match('~([\d.]+)-MariaDB~',$Qe,$z)){$Qe=$z[1];$Vf=$nd;}return$Vf&&version_compare($Qe,$Vf)>=0;}function
charset(Db$e){return(min_version("5.5.3",0,$e)?"utf8mb4":"utf8");}function
ini_bool($Lc){$W=ini_get($Lc);return(preg_match('~^(on|true|yes)$~i',$W)||(int)$W);}function
ini_bytes($Lc){$W=ini_get($Lc);switch(strtolower(substr($W,-1))){case'g':$W=(int)$W*1024;case'm':$W=(int)$W*1024;case'k':$W=(int)$W*1024;}return$W;}function
sid(){static$J;if($J===null)$J=(SID&&!($_COOKIE&&ini_bool("session.use_cookies")));return$J;}function
set_password($Y,$N,$U,$F){$_SESSION["pwds"][$Y][$N][$U]=($_COOKIE["adminer_key"]&&is_string($F)?array(encrypt_string($F,$_COOKIE["adminer_key"])):$F);}function
get_password(){$J=get_session("pwds");if(is_array($J))$J=($_COOKIE["adminer_key"]?decrypt_string($J[0],$_COOKIE["adminer_key"]):false);return$J;}function
get_val($H,$j=0,$Ta=null){$Ta=connection($Ta);$I=$Ta->query($H);if(!is_object($I))return
false;$K=$I->fetch_row();return($K?$K[$j]:false);}function
get_vals($H,$c=0){$J=array();$I=connection()->query($H);if(is_object($I)){while($K=$I->fetch_row())$J[]=$K[$c];}return$J;}function
get_key_vals($H,$f=null,$Te=true){$f=connection($f);$J=array();$I=$f->query($H);if(is_object($I)){while($K=$I->fetch_row()){if($Te)$J[$K[0]]=$K[1];else$J[]=$K[0];}}return$J;}function
get_rows($H,$f=null,$i="<p class='error'>"){$Ta=connection($f);$J=array();$I=$Ta->query($H);if(is_object($I)){while($K=$I->fetch_assoc())$J[]=$K;}elseif(!$I&&!$f&&$i&&(defined('Adminer\PAGE_HEADER')||$i=="-- "))echo$i.error()."\n";return$J;}function
unique_array($K,array$u){foreach($u
as$t){if(preg_match("~PRIMARY|UNIQUE~",$t["type"])){$J=array();foreach($t["columns"]as$v){if(!isset($K[$v]))continue
2;$J[$v]=$K[$v];}return$J;}}}function
escape_key($v){if(preg_match('(^([\w(]+)('.str_replace("_",".*",preg_quote(idf_escape("_"))).')([ \w)]+)$)',$v,$z))return$z[1].idf_escape(idf_unescape($z[2])).$z[3];return
idf_escape($v);}function
where(array$Z,array$k=array()){$J=array();foreach((array)$Z["where"]as$v=>$W){$v=bracket_escape($v,true);$c=escape_key($v);$j=idx($k,$v,array());$Ub=$j["type"];$J[]=$c.(JUSH=="sql"&&$Ub=="json"?" = CAST(".q($W)." AS JSON)":(JUSH=="pgsql"&&preg_match('~^json~',$Ub)?"::jsonb = ".q($W)."::jsonb":(JUSH=="sql"&&is_numeric($W)&&preg_match('~\.~',$W)?" LIKE ".q($W):(JUSH=="mssql"&&strpos($Ub,"datetime")===false?" LIKE ".q(preg_replace('~[_%[]~','[\0]',$W)):" = ".unconvert_field($j,q($W))))));if(JUSH=="sql"&&preg_match('~char|text~',$Ub)&&preg_match("~[^ -@]~",$W))$J[]="$c = ".q($W)." COLLATE ".charset(connection())."_bin";}foreach((array)$Z["null"]as$v)$J[]=escape_key($v)." IS NULL";return
implode(" AND ",$J);}function
where_check($W,array$k=array()){parse_str($W,$Fa);remove_slashes(array(&$Fa));return
where($Fa,$k);}function
where_link($q,$c,$X,$Rd="="){return"&where%5B$q%5D%5Bcol%5D=".urlencode($c)."&where%5B$q%5D%5Bop%5D=".urlencode(($X!==null?$Rd:"IS NULL"))."&where%5B$q%5D%5Bval%5D=".urlencode($X);}function
convert_fields(array$d,array$k,array$M=array()){$J="";foreach($d
as$v=>$W){if($M&&!in_array(idf_escape($v),$M))continue;$ka=convert_field($k[$v]);if($ka)$J
.=", $ka AS ".idf_escape($v);}return$J;}function
cookie($B,$X,$jd=2592000){header("Set-Cookie: $B=".urlencode($X).($jd?"; expires=".gmdate("D, d M Y H:i:s",time()+$jd)." GMT":"")."; path=".preg_replace('~\?.*~','',$_SERVER["REQUEST_URI"]).(HTTPS?"; secure":"")."; HttpOnly; SameSite=lax",false);}function
get_settings($Xa){parse_str($_COOKIE[$Xa],$Ue);return$Ue;}function
get_setting($v,$Xa="adminer_settings",$h=null){return
idx(get_settings($Xa),$v,$h);}function
save_settings(array$Ue,$Xa="adminer_settings"){$X=http_build_query($Ue+get_settings($Xa));cookie($Xa,$X);$_COOKIE[$Xa]=$X;}function
restart_session(){if(!ini_bool("session.use_cookies")&&(!function_exists('session_status')||session_status()==1))session_start();}function
stop_session($bc=false){$Rf=ini_bool("session.use_cookies");if(!$Rf||$bc){session_write_close();if($Rf&&@ini_set("session.use_cookies",'0')===false)session_start();}}function&get_session($v){return$_SESSION[$v][DRIVER][SERVER][$_GET["username"]];}function
set_session($v,$W){$_SESSION[$v][DRIVER][SERVER][$_GET["username"]]=$W;}function
auth_url($Y,$N,$U,$g=null){$Pf=remove_from_uri(implode("|",array_keys(SqlDriver::$drivers))."|username|ext|".($g!==null?"db|":"").($Y=='mssql'||$Y=='pgsql'?"":"ns|").session_name());preg_match('~([^?]*)\??(.*)~',$Pf,$z);return"$z[1]?".(sid()?SID."&":"").($Y!="server"||$N!=""?urlencode($Y)."=".urlencode($N)."&":"").($_GET["ext"]?"ext=".urlencode($_GET["ext"])."&":"")."username=".urlencode($U).($g!=""?"&db=".urlencode($g):"").($z[2]?"&$z[2]":"");}function
is_ajax(){return($_SERVER["HTTP_X_REQUESTED_WITH"]=="XMLHttpRequest");}function
redirect($y,$_=null){if($_!==null){restart_session();$_SESSION["messages"][preg_replace('~^[^?]*~','',($y!==null?$y:$_SERVER["REQUEST_URI"]))][]=$_;}if($y!==null){if($y=="")$y=".";header("Location: $y");exit;}}function
query_redirect($H,$y,$_,$_e=true,$Mb=true,$Rb=false,$rf=""){if($Mb){$df=microtime(true);$Rb=!connection()->query($H);$rf=format_time($df);}$af=($H?adminer()->messageQuery($H,$rf,$Rb):"");if($Rb){adminer()->error
.=error().$af.script("messagesPrint();")."<br>";return
false;}if($_e)redirect($y,$_.$af);return
true;}class
Queries{static$queries=array();static$start=0;}function
queries($H){if(!Queries::$start)Queries::$start=microtime(true);Queries::$queries[]=(preg_match('~;$~',$H)?"DELIMITER ;;\n$H;\nDELIMITER ":$H).";";return
connection()->query($H);}function
apply_queries($H,array$S,$Jb='Adminer\table'){foreach($S
as$Q){if(!queries("$H ".$Jb($Q)))return
false;}return
true;}function
queries_redirect($y,$_,$_e){$ve=implode("\n",Queries::$queries);$rf=format_time(Queries::$start);return
query_redirect($ve,$y,$_,$_e,false,!$_e,$rf);}function
format_time($df){return
sprintf('%.3f s',max(0,microtime(true)-$df));}function
relative_uri(){return
str_replace(":","%3a",preg_replace('~^[^?]*/([^?]*)~','\1',$_SERVER["REQUEST_URI"]));}function
remove_from_uri($ae=""){return
substr(preg_replace("~(?<=[?&])($ae".(SID?"":"|".session_name()).")=[^&]*&~",'',relative_uri()."&"),0,-1);}function
get_file($v,$ib=false,$lb=""){$Vb=$_FILES[$v];if(!$Vb)return
null;foreach($Vb
as$v=>$W)$Vb[$v]=(array)$W;$J='';foreach($Vb["error"]as$v=>$i){if($i)return$i;$B=$Vb["name"][$v];$xf=$Vb["tmp_name"][$v];$Va=file_get_contents($ib&&preg_match('~\.gz$~',$B)?"compress.zlib://$xf":$xf);if($ib){$df=substr($Va,0,3);if(function_exists("iconv")&&preg_match("~^\xFE\xFF|^\xFF\xFE~",$df))$Va=iconv("utf-16","utf-8",$Va);elseif($df=="\xEF\xBB\xBF")$Va=substr($Va,3);}$J
.=$Va;if($lb)$J
.=(preg_match("($lb\\s*\$)",$Va)?"":$lb)."\n\n";}return$J;}function
upload_error($i){$ud=($i==UPLOAD_ERR_INI_SIZE?ini_get("upload_max_filesize"):0);return($i?'Unable to upload a file.'.($ud?" ".sprintf('Maximum allowed file size is %sB.',$ud):""):'File does not exist.');}function
repeat_pattern($ge,$hd){return
str_repeat("$ge{0,65535}",$hd/65535)."$ge{0,".($hd%65535)."}";}function
is_utf8($W){return(preg_match('~~u',$W)&&!preg_match('~[\0-\x8\xB\xC\xE-\x1F]~',$W));}function
format_number($W){return
strtr(number_format($W,0,".",','),preg_split('~~u','0123456789',-1,PREG_SPLIT_NO_EMPTY));}function
friendly_url($W){return
preg_replace('~\W~i','-',$W);}function
table_status1($Q,$Sb=false){$J=table_status($Q,$Sb);return($J?reset($J):array("Name"=>$Q));}function
column_foreign_keys($Q){$J=array();foreach(adminer()->foreignKeys($Q)as$fc){foreach($fc["source"]as$W)$J[$W][]=$fc;}return$J;}function
fields_from_edit(){$J=array();foreach((array)$_POST["field_keys"]as$v=>$W){if($W!=""){$W=bracket_escape($W);$_POST["function"][$W]=$_POST["field_funs"][$v];$_POST["fields"][$W]=$_POST["field_vals"][$v];}}foreach((array)$_POST["fields"]as$v=>$W){$B=bracket_escape($v,true);$J[$B]=array("field"=>$B,"privileges"=>array("insert"=>1,"update"=>1,"where"=>1,"order"=>1),"null"=>1,"auto_increment"=>($v==driver()->primary),);}return$J;}function
dump_headers($Ec,$Cd=false){$J=adminer()->dumpHeaders($Ec,$Cd);$Xd=$_POST["output"];if($Xd!="text")header("Content-Disposition: attachment; filename=".adminer()->dumpFilename($Ec).".$J".($Xd!="file"&&preg_match('~^[0-9a-z]+$~',$Xd)?".$Xd":""));session_write_close();if(!ob_get_level())ob_start(null,4096);ob_flush();flush();return$J;}function
dump_csv(array$K){foreach($K
as$v=>$W){if(preg_match('~["\n,;\t]|^0.|\.\d*0$~',$W)||$W==="")$K[$v]='"'.str_replace('"','""',$W).'"';}echo
implode(($_POST["format"]=="csv"?",":($_POST["format"]=="tsv"?"\t":";")),$K)."\r\n";}function
apply_sql_function($o,$c){return($o?($o=="unixepoch"?"DATETIME($c, '$o')":($o=="count distinct"?"COUNT(DISTINCT ":strtoupper("$o("))."$c)"):$c);}function
get_temp_dir(){$J=ini_get("upload_tmp_dir");if(!$J){if(function_exists('sys_get_temp_dir'))$J=sys_get_temp_dir();else{$l=@tempnam("","");if(!$l)return'';$J=dirname($l);unlink($l);}}return$J;}function
file_open_lock($l){if(is_link($l))return;$n=@fopen($l,"c+");if(!$n)return;@chmod($l,0660);if(!flock($n,LOCK_EX)){fclose($n);return;}return$n;}function
file_write_unlock($n,$fb){rewind($n);fwrite($n,$fb);ftruncate($n,strlen($fb));file_unlock($n);}function
file_unlock($n){flock($n,LOCK_UN);fclose($n);}function
first(array$ja){return
reset($ja);}function
password_file($Ya){$l=get_temp_dir()."/adminer.key";if(!$Ya&&!file_exists($l))return'';$n=file_open_lock($l);if(!$n)return'';$J=stream_get_contents($n);if(!$J){$J=rand_string();file_write_unlock($n,$J);}else
file_unlock($n);return$J;}function
rand_string(){return
md5(uniqid(strval(mt_rand()),true));}function
select_value($W,$x,array$j,$pf){if(is_array($W)){$J="";foreach($W
as$Yc=>$V)$J
.="<tr>".($W!=array_values($W)?"<th>".h($Yc):"")."<td>".select_value($V,$x,$j,$pf);return"<table>$J</table>";}if(!$x)$x=adminer()->selectLink($W,$j);if($x===null){if(is_mail($W))$x="mailto:$W";if(is_url($W))$x=$W;}$J=adminer()->editVal($W,$j);if($J!==null){if(!is_utf8($J))$J="\0";elseif($pf!=""&&is_shortable($j))$J=shorten_utf8($J,max(0,+$pf));else$J=h($J);}return
adminer()->selectVal($J,$x,$j,$W);}function
is_blob(array$j){return
preg_match('~blob|bytea|raw|file~',$j["type"])&&!in_array($j["type"],idx(driver()->structuredTypes(),'User types',array()));}function
is_mail($zb){$la='[-a-z0-9!#$%&\'*+/=?^_`{|}~]';$sb='[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])';$ge="$la+(\\.$la+)*@($sb?\\.)+$sb";return
is_string($zb)&&preg_match("(^$ge(,\\s*$ge)*\$)i",$zb);}function
is_url($P){$sb='[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])';return
preg_match("~^(https?)://($sb?\\.)+$sb(:\\d+)?(/.*)?(\\?.*)?(#.*)?\$~i",$P);}function
is_shortable(array$j){return
preg_match('~char|text|json|lob|geometry|point|linestring|polygon|string|bytea|hstore~',$j["type"]);}function
host_port($N){return(preg_match('~^(\[(.+)]|([^:]+)):([^:]+)$~',$N,$z)?array($z[2].$z[3],$z[4]):array($N,''));}function
count_rows($Q,array$Z,$Tc,array$p){$H=" FROM ".table($Q).($Z?" WHERE ".implode(" AND ",$Z):"");return($Tc&&(JUSH=="sql"||count($p)==1)?"SELECT COUNT(DISTINCT ".implode(", ",$p).")$H":"SELECT COUNT(*)".($Tc?" FROM (SELECT 1$H GROUP BY ".implode(", ",$p).") x":$H));}function
slow_query($H){$g=adminer()->database();$sf=adminer()->queryTimeout();$We=driver()->slowQuery($H,$sf);$f=null;if(!$We&&support("kill")){$f=connect();if($f&&($g==""||$f->select_db($g))){$ad=get_val(connection_id(),0,$f);echo
script("const timeout = setTimeout(() => { ajax('".js_escape(ME)."script=kill', function () {}, 'kill=$ad&token=".get_token()."'); }, 1000 * $sf);");}}ob_flush();flush();$J=@get_key_vals(($We?:$H),$f,false);if($f){echo
script("clearTimeout(timeout);");ob_flush();flush();}return$J;}function
get_token(){$ye=rand(1,1e6);return($ye^$_SESSION["token"]).":$ye";}function
verify_token(){list($yf,$ye)=explode(":",$_POST["token"]);return($ye^$_SESSION["token"])==$yf;}function
lzw_decompress($ya){$pb=256;$za=8;$La=array();$Ee=0;$Fe=0;for($q=0;$q<strlen($ya);$q++){$Ee=($Ee<<8)+ord($ya[$q]);$Fe+=8;if($Fe>=$za){$Fe-=$za;$La[]=$Ee>>$Fe;$Ee&=(1<<$Fe)-1;$pb++;if($pb>>$za)$za++;}}$ob=range("\0","\xFF");$J="";$cg="";foreach($La
as$q=>$Ka){$yb=$ob[$Ka];if(!isset($yb))$yb=$cg.$cg[0];$J
.=$yb;if($q)$ob[]=$cg.$yb[0];$cg=$yb;}return$J;}function
script($Ye,$zf="\n"){return"<script".nonce().">$Ye</script>$zf";}function
script_src($Qf,$jb=false){return"<script src='".h($Qf)."'".nonce().($jb?" defer":"")."></script>\n";}function
nonce(){return' nonce="'.get_nonce().'"';}function
input_hidden($B,$X=""){return"<input type='hidden' name='".h($B)."' value='".h($X)."'>\n";}function
input_token(){return
input_hidden("token",get_token());}function
target_blank(){return' target="_blank" rel="noreferrer noopener"';}function
h($P){return
str_replace("\0","&#0;",htmlspecialchars($P,ENT_QUOTES,'utf-8'));}function
nl_br($P){return
str_replace("\n","<br>",$P);}function
checkbox($B,$X,$Ga,$bd="",$Pd="",$Ja="",$dd=""){$J="<input type='checkbox' name='$B' value='".h($X)."'".($Ga?" checked":"").($dd?" aria-labelledby='$dd'":"").">".($Pd?script("qsl('input').onclick = function () { $Pd };",""):"");return($bd!=""||$Ja?"<label".($Ja?" class='$Ja'":"").">$J".h($bd)."</label>":$J);}function
optionlist($C,$Me=null,$Sf=false){$J="";foreach($C
as$Yc=>$V){$Td=array($Yc=>$V);if(is_array($V)){$J
.='<optgroup label="'.h($Yc).'">';$Td=$V;}foreach($Td
as$v=>$W)$J
.='<option'.($Sf||is_string($v)?' value="'.h($v).'"':'').($Me!==null&&($Sf||is_string($v)?(string)$v:$W)===$Me?' selected':'').'>'.h($W);if(is_array($V))$J
.='</optgroup>';}return$J;}function
html_select($B,array$C,$X="",$Od="",$dd=""){static$bd=0;$cd="";if(!$dd&&substr($C[""],0,1)=="("){$bd++;$dd="label-$bd";$cd="<option value='' id='$dd'>".h($C[""]);unset($C[""]);}return"<select name='".h($B)."'".($dd?" aria-labelledby='$dd'":"").">".$cd.optionlist($C,$X)."</select>".($Od?script("qsl('select').onchange = function () { $Od };",""):"");}function
html_radios($B,array$C,$X="",$Pe=""){$J="";foreach($C
as$v=>$W)$J
.="<label><input type='radio' name='".h($B)."' value='".h($v)."'".($v==$X?" checked":"").">".h($W)."</label>$Pe";return$J;}function
confirm($_="",$Ne="qsl('input')"){return
script("$Ne.onclick = () => confirm('".($_?js_escape($_):'Are you sure?')."');","");}function
print_fieldset($r,$gd,$Yf=false){echo"<fieldset><legend>","<a href='#fieldset-$r'>$gd</a>",script("qsl('a').onclick = partial(toggle, 'fieldset-$r');",""),"</legend>","<div id='fieldset-$r'".($Yf?"":" class='hidden'").">\n";}function
bold($_a,$Ja=""){return($_a?" class='active $Ja'":($Ja?" class='$Ja'":""));}function
js_escape($P){return
addcslashes($P,"\r\n'\\/");}function
pagination($E,$db){return" ".($E==$db?$E+1:'<a href="'.h(remove_from_uri("page").($E?"&page=$E".($_GET["next"]?"&next=".urlencode($_GET["next"]):""):"")).'">'.($E+1)."</a>");}function
hidden_fields(array$se,array$Gc=array(),$oe=''){$J=false;foreach($se
as$v=>$W){if(!in_array($v,$Gc)){if(is_array($W))hidden_fields($W,array(),$v);else{$J=true;echo
input_hidden(($oe?$oe."[$v]":$v),$W);}}}return$J;}function
hidden_fields_get(){echo(sid()?input_hidden(session_name(),session_id()):''),(SERVER!==null?input_hidden(DRIVER,SERVER):""),input_hidden("username",$_GET["username"]);}function
file_input($Nc){$rd="max_file_uploads";$sd=ini_get($rd);$Nf="upload_max_filesize";$Of=ini_get($Nf);return(ini_bool("file_uploads")?$Nc.script("qsl('input[type=\"file\"]').onchange = partialArg(fileChange, "."$sd, '".sprintf('Increase %s.',"$rd = $sd")."', ".ini_bytes("upload_max_filesize").", '".sprintf('Increase %s.',"$Nf = $Of")."')"):'File uploads are disabled.');}function
enum_input($T,$b,array$j,$X,$Bb=""){preg_match_all("~'((?:[^']|'')*)'~",$j["length"],$pd);$oe=($j["type"]=="enum"?"val-":"");$Ga=(is_array($X)?in_array("null",$X):$X===null);$J=($j["null"]&&$oe?"<label><input type='$T'$b value='null'".($Ga?" checked":"")."><i>$Bb</i></label>":"");foreach($pd[1]as$W){$W=stripcslashes(str_replace("''","'",$W));$Ga=(is_array($X)?in_array($oe.$W,$X):$X===$W);$J
.=" <label><input type='$T'$b value='".h($oe.$W)."'".($Ga?' checked':'').'>'.h(adminer()->editVal($W,$j)).'</label>';}return$J;}function
input(array$j,$X,$o,$qa=false){$B=h(bracket_escape($j["field"]));echo"<td class='function'>";if(is_array($X)&&!$o){$X=json_encode($X,128|64|256);$o="json";}$De=(JUSH=="mssql"&&$j["auto_increment"]);if($De&&!$_POST["save"])$o=null;$kc=(isset($_GET["select"])||$De?array("orig"=>'original'):array())+adminer()->editFunctions($j);$Fb=driver()->enumLength($j);if($Fb){$j["type"]="enum";$j["length"]=$Fb;}$qb=stripos($j["default"],"GENERATED ALWAYS AS ")===0?" disabled=''":"";$b=" name='fields[$B]".($j["type"]=="enum"||$j["type"]=="set"?"[]":"")."'$qb".($qa?" autofocus":"");echo
driver()->unconvertFunction($j)." ";$Q=$_GET["edit"]?:$_GET["select"];if($j["type"]=="enum")echo
h($kc[""])."<td>".adminer()->editInput($Q,$j,$b,$X);else{$tc=(in_array($o,$kc)||isset($kc[$o]));echo(count($kc)>1?"<select name='function[$B]'$qb>".optionlist($kc,$o===null||$tc?$o:"")."</select>".on_help("event.target.value.replace(/^SQL\$/, '')",1).script("qsl('select').onchange = functionChange;",""):h(reset($kc))).'<td>';$Nc=adminer()->editInput($Q,$j,$b,$X);if($Nc!="")echo$Nc;elseif(preg_match('~bool~',$j["type"]))echo"<input type='hidden'$b value='0'>"."<input type='checkbox'".(preg_match('~^(1|t|true|y|yes|on)$~i',$X)?" checked='checked'":"")."$b value='1'>";elseif($j["type"]=="set")echo
enum_input("checkbox",$b,$j,(is_string($X)?explode(",",$X):$X));elseif(is_blob($j)&&ini_bool("file_uploads"))echo"<input type='file' name='fields-$B'>";elseif($o=="json"||preg_match('~^jsonb?$~',$j["type"]))echo"<textarea$b cols='50' rows='12' class='jush-js'>".h($X).'</textarea>';elseif(($of=preg_match('~text|lob|memo~i',$j["type"]))||preg_match("~\n~",$X)){if($of&&JUSH!="sqlite")$b
.=" cols='50' rows='12'";else{$L=min(12,substr_count($X,"\n")+1);$b
.=" cols='30' rows='$L'";}echo"<textarea$b>".h($X).'</textarea>';}else{$Ef=driver()->types();$wd=(!preg_match('~int~',$j["type"])&&preg_match('~^(\d+)(,(\d+))?$~',$j["length"],$z)?((preg_match("~binary~",$j["type"])?2:1)*$z[1]+($z[3]?1:0)+($z[2]&&!$j["unsigned"]?1:0)):($Ef[$j["type"]]?$Ef[$j["type"]]+($j["unsigned"]?0:1):0));if(JUSH=='sql'&&min_version(5.6)&&preg_match('~time~',$j["type"]))$wd+=7;echo"<input".((!$tc||$o==="")&&preg_match('~(?<!o)int(?!er)~',$j["type"])&&!preg_match('~\[\]~',$j["full_type"])?" type='number'":"")." value='".h($X)."'".($wd?" data-maxlength='$wd'":"").(preg_match('~char|binary~',$j["type"])&&$wd>20?" size='".($wd>99?60:40)."'":"")."$b>";}echo
adminer()->editHint($Q,$j,$X);$Yb=0;foreach($kc
as$v=>$W){if($v===""||!$W)break;$Yb++;}if($Yb&&count($kc)>1)echo
script("qsl('td').oninput = partial(skipOriginal, $Yb);");}}function
process_input(array$j){if(stripos($j["default"],"GENERATED ALWAYS AS ")===0)return;$s=bracket_escape($j["field"]);$o=idx($_POST["function"],$s);$X=idx($_POST["fields"],$s);if($j["type"]=="enum"||driver()->enumLength($j)){$X=$X[0];if($X=="orig")return
false;if($X=="null")return"NULL";$X=substr($X,4);}if($j["auto_increment"]&&$X=="")return
null;if($o=="orig")return(preg_match('~^CURRENT_TIMESTAMP~i',$j["on_update"])?idf_escape($j["field"]):false);if($o=="NULL")return"NULL";if($j["type"]=="set")$X=implode(",",(array)$X);if($o=="json"){$o="";$X=json_decode($X,true);if(!is_array($X))return
false;return$X;}if(is_blob($j)&&ini_bool("file_uploads")){$Vb=get_file("fields-$s");if(!is_string($Vb))return
false;return
driver()->quoteBinary($Vb);}return
adminer()->processInput($j,$X,$o);}function
search_tables(){$_GET["where"][0]["val"]=$_POST["query"];$Oe="<ul>\n";foreach(table_status('',true)as$Q=>$R){$B=adminer()->tableName($R);if(isset($R["Engine"])&&$B!=""&&(!$_POST["tables"]||in_array($Q,$_POST["tables"]))){$I=connection()->query("SELECT".limit("1 FROM ".table($Q)," WHERE ".implode(" AND ",adminer()->selectSearchProcess(fields($Q),array())),1));if(!$I||$I->fetch_row()){$qe="<a href='".h(ME."select=".urlencode($Q)."&where[0][op]=".urlencode($_GET["where"][0]["op"])."&where[0][val]=".urlencode($_GET["where"][0]["val"]))."'>$B</a>";echo"$Oe<li>".($I?$qe:"<p class='error'>$qe: ".error())."\n";$Oe="";}}}echo($Oe?"<p class='message'>".'No tables.':"</ul>")."\n";}function
on_help($Qa,$Ve=0){return
script("mixin(qsl('select, input'), {onmouseover: function (event) { helpMouseover.call(this, event, $Qa, $Ve) }, onmouseout: helpMouseout});","");}function
edit_form($Q,array$k,$K,$Mf,$i=''){$mf=adminer()->tableName(table_status1($Q,true));page_header(($Mf?'Edit':'Insert'),$i,array("select"=>array($Q,$mf)),$mf);adminer()->editRowPrint($Q,$k,$K,$Mf);if($K===false){echo"<p class='error'>".'No rows.'."\n";return;}echo"<form action='' method='post' enctype='multipart/form-data' id='form'>\n";if(!$k)echo"<p class='error'>".'You have no privileges to update this table.'."\n";else{echo"<table class='layout'>".script("qsl('table').onkeydown = editingKeydown;");$qa=!$_POST;foreach($k
as$B=>$j){echo"<tr><th>".adminer()->fieldName($j);$h=idx($_GET["set"],bracket_escape($B));if($h===null){$h=$j["default"];if($j["type"]=="bit"&&preg_match("~^b'([01]*)'\$~",$h,$Be))$h=$Be[1];if(JUSH=="sql"&&preg_match('~binary~',$j["type"]))$h=bin2hex($h);}$X=($K!==null?($K[$B]!=""&&JUSH=="sql"&&preg_match("~enum|set~",$j["type"])&&is_array($K[$B])?implode(",",$K[$B]):(is_bool($K[$B])?+$K[$B]:$K[$B])):(!$Mf&&$j["auto_increment"]?"":(isset($_GET["select"])?false:$h)));if(!$_POST["save"]&&is_string($X))$X=adminer()->editVal($X,$j);$o=($_POST["save"]?idx($_POST["function"],$B,""):($Mf&&preg_match('~^CURRENT_TIMESTAMP~i',$j["on_update"])?"now":($X===false?null:($X!==null?'':'NULL'))));if(!$_POST&&!$Mf&&$X==$j["default"]&&preg_match('~^[\w.]+\(~',$X))$o="SQL";if(preg_match("~time~",$j["type"])&&preg_match('~^CURRENT_TIMESTAMP~i',$X)){$X="";$o="now";}if($j["type"]=="uuid"&&$X=="uuid()"){$X="";$o="uuid";}if($qa!==false)$qa=($j["auto_increment"]||$o=="now"||$o=="uuid"?null:true);input($j,$X,$o,$qa);if($qa)$qa=false;echo"\n";}if(!support("table")&&!fields($Q))echo"<tr>"."<th><input name='field_keys[]'>".script("qsl('input').oninput = fieldChange;")."<td class='function'>".html_select("field_funs[]",adminer()->editFunctions(array("null"=>isset($_GET["select"]))))."<td><input name='field_vals[]'>"."\n";echo"</table>\n";}echo"<p>\n";if($k){echo"<input type='submit' value='".'Save'."'>\n";if(!isset($_GET["select"]))echo"<input type='submit' name='insert' value='".($Mf?'Save and continue edit':'Save and insert next')."' title='Ctrl+Shift+Enter'>\n",($Mf?script("qsl('input').onclick = function () { return !ajaxForm(this.form, '".'Saving'."‚Ä¶', this); };"):"");}echo($Mf?"<input type='submit' name='delete' value='".'Delete'."'>".confirm()."\n":"");if(isset($_GET["select"]))hidden_fields(array("check"=>(array)$_POST["check"],"clone"=>$_POST["clone"],"all"=>$_POST["all"]));echo
input_hidden("referer",(isset($_POST["referer"])?$_POST["referer"]:$_SERVER["HTTP_REFERER"])),input_hidden("save",1),input_token(),"</form>\n";}function
shorten_utf8($P,$hd=80,$if=""){if(!preg_match("(^(".repeat_pattern("[\t\r\n -\x{10FFFF}]",$hd).")($)?)u",$P,$z))preg_match("(^(".repeat_pattern("[\t\r\n -~]",$hd).")($)?)",$P,$z);return
h($z[1]).$if.(isset($z[2])?"":"<i>‚Ä¶</i>");}function
icon($Dc,$B,$Cc,$tf){return"<button type='submit' name='$B' title='".h($tf)."' class='icon icon-$Dc'><span>$Cc</span></button>";}if(isset($_GET["file"])){if(substr(VERSION,-4)!='-dev'){if($_SERVER["HTTP_IF_MODIFIED_SINCE"]){header("HTTP/1.1 304 Not Modified");exit;}header("Expires: ".gmdate("D, d M Y H:i:s",time()+365*24*60*60)." GMT");header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");header("Cache-Control: immutable");}@ini_set("zlib.output_compression",'1');if($_GET["file"]=="default.css"){header("Content-Type: text/css; charset=utf-8");echo
lzw_decompress("h:Má±h¥ƒgÃ–±‹Õå\"P—iê“mÑôcQCa§È	2√≥Èàﬁd<ûÃfÛaº‰:;NBàqúR;1Lf≥9»ﬁu7&)§l;3Õ—Ò»¿J/ãÜCQX r2M∆a‰i0õÑÉ)∞Ïe:Lu√ùhÊ-9’Õ23l»Œi7Ü≥m‡Zw4ôÜÅ—ö<-ï“Ã¥π!ÜU,óåF√©îvt2ûëS,¨‰a¥“áFÍVX˙aòNq„)ì-ó÷Œ«úhÍ:n5éç˚9»Y®;jµî-ﬁ˜_ë9kr˘úŸì;.–tTqÀo¶0ã≥≠÷ÚÆ{ÌÛy˘˝\rÁHnÏçGSô†Zh≤ú;ºi^¿ux¯WŒíC@ƒˆ§©kÄ“=°–b©À‚Ïº/Aÿ‡0§+¬(⁄¡∞l¬…¬\\Í†√xË:\rË¿b8\0Êñ0!\0F∆\nBîÕé„(“3†\r\\∫çÅ€Í»ÑaºÑú'I‚|Í(iö\nã\r©∏˙4O¸g@ê4¡CíÓºÜ∫@@Ü!ƒQB∞›	¬∞∏c§ ¬Øƒq,\r1EhË»&2PZá¶iG˚H9Gí\"vûßÍí¢££§ú4rî∆ÒÕD–R§\nÜpJÎ-Aì|/.ØcÍìDu∑è£§ˆ:,ò =∞¢R≈]U5•mV¡kÕLLQ@-\\™¶Àå@9¡„%⁄Sùr¡ŒÒMPD„¬Ia\rÉ(YY\\„@Xıp√Í:ç£p˜léLC ó≈ÒçË∏ÉÕ O,\r∆2]7ú?m06‰ªp‹T—Õa“•Cú;_Àó—y»¥dë>®≤bnÖ´nº‹£3˜XæÄˆ8\rÌ[ÀÄ-)€i>V[Y„y&L3Ø#ÃX|’	ÜX†\\√π`ÀCßÁòÂ#—ŸH…Ã2 2.#†ˆãZÉ`¬<æ„sÆ∑π™√í£∫\0uúh÷æó•M≤Õ_\niZeO/C”í_Ü`3›Ú1>ã=Å–k3è£ÖâR/;‰/d€‹\0˙ãå„ﬁ⁄µm˘˙Úæ§7/´÷êAŒXÉ¬ˇÑ∞ì√q.Ωs·L£˝ó :\$…F¢ó∏™æ£Çwâ8Ûﬂæ~´H‘jÖ≠\"®ºúïπ‘≥7gSı‰±‚FLÈŒØÁQÚ_§íO'Wÿˆ]c=˝5æ1X~7;òôi˛¥\rÌ*\ní®JS1Z¶ô¯û£ÿ∆ﬂÕcÂÇêtú¸A‘VÌê86f–d√y;Yè]©ızI¿p°—˚ßcâ3ÆYÀ]}¬ò@°\$.+î1∂'>Z√cpd‡È“GLÊ·Ñ#kÙ8PzúY“Auœv›]s9â—ÿ_AqŒ¡Ñ:Ü∆≈\nKÄhBº;≠÷äXbAHq,ê‚CI…`êÜÇÁjπS[Àå∂1∆V”räÒ‘;∂pﬁB√€)#Èêâ;4ÃHÒ“/*’<¬3L†¡;lf™\n∂s\$K`–}∆Ù’î£éæ7Éjx`dñ%j]†∏4úóY§ñHbY†ÿJ`§GG†í.≈‹KÇÚf I©)2¬äÅMf÷∏›XâRCâ∏Ã±V,©€—~g\0ËÇ‡g6›:ı[jÌ1HΩ:AlIq©u3\"ôÍÊÅq§Ê|8<9s'„Q]J |–\0¬`p†≥ÓÉ´âjfÑO∆b–…˙¨®q¨¢\$È©≤√1Jπ>RúH(«îq\n#räêí‡@ûe(yÛVJµ0°Q“à£Úà6ÜPÊ[C:∑G‰ºûë†›4©ë“^û”√PZäµ\\¥ëË(\nê÷)ö~¶¥∞9R%◊Sj∑{çâ7‰0ﬁ_ö«s	z|8≈HÍ	\"@‹#9DVL≈\$H5‘WJ@óÖzÆaøJ ƒ^	ë)Æ2\nQv¿‘]Î«Üƒ¡òâj (A∏”∞BB05¥6ÜbÀ∞][åËk™AïwvkgÙ∆¥ˆ∫’+k[jmÑzc∂}ËMyDZiÌ\$5eò´ ∑∞Å∫	îAò†CY%.WÄb*ÎÆºÇ.≠ŸÛq/%}BÃXà≠ÁZV337á ªaôùÑÄ∫ÚﬁwW[·LéQ ﬁ≤¸_»è2`«1I—i,˜Êõ£íMf&(s-ò‰òÎ¬Aƒ∞ÿ*îîDwÿƒTN¿…ª≈jX\$Èx™+;–ÀF⁄93µJk¬ôS;∑ß¡qR{>lû;B1A»I‚b)†ù(6±≠r˜\r›\r⁄áí⁄ÇÏZëR^SOy/ìﬁM#∆œ9{kÑ‡Í∏v\"˙KC‚JÉ®rEo\0¯Ã\\,—|êfaÕöÜ≥hIì©/oÃ4ƒk^pÓ1H»^ìèçÕph«°V¡vox@¯`Ìgü&è(˘à≠¸;õÉ~«çzÃ6◊8Ø*∞∆‹5Æ‹â±E†¡¬pÜÈ‚Ó”òò§¥3ìˆ≈ÜgüôrD—LÛ)4g{ªà‰ΩÂ≥©óLéö&˙>ËÑª¢Åÿ⁄ZÏ7°\0˙∞Ãä@◊–”€úff≈RVh÷ù≤ÁIä€àΩ‚r”w)ã†ÇÑ=x^ò,kíü2Ù“›ìj‡bÎl0uÎû\"¨fp®∏1ÒRIøÉz[]§wÅpN6dI™zÎıÂn.7X{;¡»3ÿÀ-I	ã‚˚¸7pj√ù¢Ré#™,˘_-–¸¬[Û>3¿\\ÊÍ€WqﬁqîJ÷òÅuh£á–FbL¡K‘ÂÁyVƒæ©¶√ﬁ—ïÆµ™ù¸VúÓ√f{K}S† ﬁùÖâM˛á∑ÕÄº¶.M∂\\™ix∏b¡°ê1á+£Œ±?<≈3Í~H˝”\$˜\\–2€\$Ó eÿ6t‘OÃà„\$sºº©xƒ˛xèïÛßC·nSkVƒ…=z6Ωâ° '√¶‰Naü¢÷∏hå‹¸∏∫±˝ØR§Âô£8géâ¢‰ w:_≥Ó≠ÌˇÍ“íIRK√ù®.ΩnkVU+dwjôß%≥`#,{ùÈÜ≥À ÉYá˝◊ı(o’æ….®cÇ0g‚DXOkÜ7ÆËK‰Œl“Õhx;œÿè ›ÉL˚¥\$09*ñ9 ‹hNr¸M’.>\0ÿrP9Ô\$»g	\0\$\\FÛ*≤d'ŒıLÂ:ãb˙ó4è2¿Ù¢9¿@¬HnbÏ-§ÛE #ƒú…√®\0¿pYÇÍ® tÕ ÿ\n5.©‡ ‚Ó\$op†lÄX\n@`\rÄé	‡à\rÄ–†Œ ¶ î†Ç û‡Í€`î\r†¥\r†£`Ç`ê†Ñ0Âp‰	ëﬁ@ì\0í¿–	 V\0Ú`f¿œ¿™\0§†ŒfÄ\0j\n†f`‚	 Æ\n`¥@ò\$n=`Ü\0»¿É‡nI–\$ˇP(¬d'ÀÙÑƒ‡∑g…\nê¨4±\n0∑§à.0√pÀ“\r\0á`ñ1`ì‡Œ\n\0_ ÛqÒ1qµ`ﬂ\0°¿îÇ†‰‡òÜ\0¢\n@‚Äû fÕPÊÄÊ†R«†ﬁ«ÏÇÄ@Ÿr«FàòØh\rÄ@J∂—^LNÀ!¿È\"\n“ƒe ]r: Z7“9#\$0¨µ\"g⁄≠çtîRB◊ç|ë/º#Ìî◊∏Dí1\"ÆFfá\"n∫ÚÊ(Yp`WÖîY∆ë“]\$¿FF®Ø‹Rn\r‡w!MrÏÊK≤*s%S\$≤ ƒ®.s*G*R©(=+ãﬁã	\n)“d˚Ú£*mpëÇ\$r–Ï‰◊\$î‹¿Î-‚?.2©+r:~≤–ÇI69+ú4Hºh†˙\nz\"–(,2 +DˆjuÂt@q.†≥≤ΩR√&iù,kJñr`Ñc¿’\"¢CI—	Í‚z8⁄çå•æ€\r¥öØ8Í“¯›fÉ¢øÎ√.\"˙÷À‰õÍÆ”*h(ÂÈ\0ÙOâ™™ÕÄ’ r| ﬁÖM\n–Âæ≠o|LJ™Í≤v1N¥‹3E(ÑR\".fh+FW/“ŒIöŒì~/)¿⁄¶\rƒâÔ<¿€=h1âb]¢‘&≈iúÚ-éÚmRÙÁ?‰0ÕÓ˙ì¶–‰‘Ô ÍÔl¶ìâÑì ◊Æ◊@Œ⁄úo~Ú≥D“ÏóT7t	>k')ç@\$E/”G''\$1+Ó*í„) ŸE≤˘B‘a0\rqDèÅ.Ó±'≥ƒ˚‰ª?S˘=Ow*ì…Fíó>o\r>¥,`AIB]?£˜*ìr1‚¿ﬂÇæÎSÿ‡R¯]sNlL©B/Ô;Ó≤◊Âä˚ØæÏ≠)Ñ!>ìÌ<fõHíy<4„,Rp√4ùOr;J2õJtÛ.IPrŒÒä∂5¢Íäüè÷*rQ ˙†");}elseif($_GET["file"]=="dark.css"){header("Content-Type: text/css; charset=utf-8");echo
lzw_decompress("h:Má±h¥ƒg∆ê»h0¡L–Å‡d91¢S!§€	çFÉ!∞Ê\"-6NêëÄƒbdçGg”∞¬:;Nr£)êˆc7õ\rÁ(Hÿb81òÜs9º§‹k\rÁc) m8ùOïùVA°¬c1Åîc34Of*í™-†P®Ç1©îr41ŸÓ6òÃd2å÷ÅïÆ€oΩ‹Ã#3óâñB«f#	å÷g9Œ¶Íÿåfc\r«Iô–¬b6EáC&¨–,ébuƒÍm7aéV„ï¬¡s≤ç#m!ÙËhµÂr˘úﬁv\\3\rL:SAî¬dk5›n«∑◊Ïö˝ aFÜ∏3Èò“e6fS¶ÎyæÛ¯r!«L˙†-ŒK,Ã3L‚@∫ìJ∂ÉÀ≤¢*J†‰Ïµ£§êÇª	∏óπ¡öb©cË‡9≠àÍ9π§Ê@œ‘Ëø√H‹8£†\\∑√Í6>´`≈é∏ﬁ;áAà‡<Tô'®p&q¥qEàÍ4≈\rl≠Ö√h¬<5#pœ»R —#IÑ›%ÑÍfBIÿﬁ‹≤î®>Ö ´29<´ÂCÓj2ØÓª¶∂7j¨ì8j“Ïc(n‘ƒÁ?(a\0≈@î5*3:Œ¥Ê6å£òÊ0å„-‡A¿lLõïP∆4@ …∞Í\$°H•4†n31∂Ê1ÕtÚ0Æ·Õô9åÉÈWO!®rº⁄‘ÿ‹€’ËH»Ü£√9åQ∞¬96ËF±¨´<¯7∞\rú-xC\n ‹„Æ@“¯Öê‹‘É:\$i‹ÿ∂m´™À4ÌKid¨≤{\n6\rñÖçxhÀã‚#^'4V¯@aÕ«<¥#h0¶SÊ-Öc∏÷9â+pä´äaû2‘cyÜhÆBO\$¡Á9ˆwáiXõ…î˘VY9ç*r˜Htm	Å@b÷—|@¸/ÅÄlí\$z¶≠†+‘%p2lãò….ıÿ˙’€Ïƒ7Ô;«&{¿ÀmÑÄX®C<l9Ì6x9ÔmÏÚ§ÉØ¿≠7R¸¿0\\Í4Œ˜P»)A»o¿éxÑƒ⁄qÕO#∏•Å»f[;ª™6~P€\råa∏ TùGT0ÑËÏu∏ﬁüæ≥ﬁ\n3\\ \\ éÉJ©ud™CG¿ß©PZ˜>ì≥¡˚d8÷“®ËÈÒΩÔçÂÙC?VÖ∑dL≈L.(tiÉí≠>´,ÙÉ÷L¿");}elseif($_GET["file"]=="functions.js"){header("Content-Type: text/javascript; charset=utf-8");echo
lzw_decompress("':úÃ¢ô–‰i1ù„≥1‘›	4õÕ¿£âÃQ6a&Ûê∞«:OAIÏ‰e:NF·D|›!ëüÜCyåÍm2À≈\"„â‘ r<îÃ±òŸ /Cç#ÇëŸˆ:DbqSeâJéÀ¶C‹∫\n\n°ú«±S\rZìèH\$RA‹ûS+XKvtd‹g:£Ì6üâEvX≈û≥jë…m“©ej◊2öMß©‰˙ÅB´«& ÆãLßC∞3ÅÑÂQ0’L∆È-xË\n”ÏDë»¬yNa‰Pn:Áõº‰ËsùúÕêÉ(†cL≈‹/ıê£(∆5{ùﬁÙQy4ú¯g-ñÇ˝¢Íi4⁄Éf–Œ(’ÎbU˝éœk∑Óo7‹&„∫√§Ù*ACbíæ¢ÿ`.á≠ä€\rŒ–‹¸ªœƒ˙ºÕ\n†©Ch“<\r)`Ëÿ•`Ê7•C íå»‚Z˘µ„X <ÅQ≈1X˜ºâ@∑0dp9EQ¸fæè∞”Fÿ\râ‰!çÉÊã(hÙ£)â√\np'#ƒå§£HÃ(i*Ür∏Ê&<#¢Ê7K»»~å# »áA:N6ç„∞ ã©l’,ß\rîÙÅJPŒ3£!@“2>Cræ°¨h∞NÑ·]¶(a0M3Õ2î◊6Ö‘UÊÑ„E2'!<∑¬#3RÅ<€èç„X“Ê‘CHŒ7É#n‰+±Äa\$!Ë‹2é‡Pà0§.∞wd°r:Yˆç®ÈE≤ÊÖ!]Ñ<πöj‚•Û@ﬂ\\◊plß_\r¡Z∏èÄ“ì¨TÕ©Z…sÚ3\"≤~9¿©≥j„âPÿ)QìYb›ïDÎYcêø`àêz·cûµ—®Ã€'Î#tìBOh¢*2ˇÖ<≈íOÍfg-Z£úà’#†Ë8a–^é˙+r2bâ¯\\é·~0©·˛ì•˘‡W©∏¡ﬁnúŸp!#ï`ÂçÎZˆ∏6∂1ê2◊√@È≤ky»∆9\rÏ‰B3ÁÉpﬁÖÓ6∞Ë<£!pÔGØ9‡nëoõ6sø#Fÿ3ÌŸ‡bA® 6Ò9¶˝¿Z£#¬ﬁ6˚ %?ás®»\"œ…|ÿÇß)˛búJc\rªéåΩNﬁs…€ih8œáπÊ›üË:ä;Ë˙HÂﬁåıuãI5˚@Ë1çÓùÅ™AËPaH^\$H◊v„÷@√õL~ó®˘b9é'ß¯ø±çS?P–-ØòÚò0çC\nRÚmÃ4áﬁ”»ì:¿ı‹‘∏Ô2ÚÃ4úµh(k\njIä»6\"òEYà#ÅπWír™\rÅëG8£@t–·ûÅX‘ì‚ÃBS\nc0…kÇC I\r ∞<u`A!Û)–‘2î÷C¢\0=áÅæ Ê·‰Pà1ë”¢K!π!ÜÂüpƒIs—,6‚d√È…i1+∞»‚‘kâÄÍ<ï∏^Å	·\né…20¥F‘â_\$Î)f\0†§C8E^¨ƒ/3W!◊ê)åuô*‰‘Ë&\$Íî2êY\n©]íÑEkÒDV®\$ÔJ≤íáxTse!êRYª RôÉ`=LÚ∏„‡ﬁ´\nl_.!≤V!¬\r\nH–kê≤\$◊ê`{1	|±ê†∞êi<jRrPTG|éÇw©4b¥\râ°«4d§,ßE°»6©‰œ<√h[NÜq@Oi◊>'—©\rä•êÛó;¶]#ìÊ}–0ªASIöJd—A/Q¡ê¥ê‚∏µ¬@t\r•UGÇƒ_Gû<ÈÕ<y-I…zÚÑ§ù–\"†P¬‡B\0˝Ì¿»¡úq`ëÔvAÉàaÃ°JÂ†R‰ Æ)åÖJB.¶T‹ÒL°Óy¢˜†çCppç\0(7ÜcYYïaç®MÄÈ1ïem4”c¢∏r£´S)oÒÕ‡ÇpÊC!IÜºæS¬úbç0mÏÒé(dìEHú¯ö∏ﬂ≥ÑXã™£/¨ïôP©Ë¯y∆XéÈ85»“\$+ó÷ñª≤êgdËÄˆŒŒy›‹œù≥J◊ÿÎ ¢lEì¢urÃ,dCXç}e¨Ï≈•ı´mÉ]à–2†ÃΩ»(-zÅ¶ÇèZÂ˙;IˆÓº\\ä) ,ç\n§>Ú)∑û§Ê\rVS\njx*w`‚¥∑SFiÃ”dØº,ª·–Z¬JFM}–ä ¿Ü\\ZæPÏ›`πzÿZ˚E]Ìd§î…üOÎcm‘Å]¿ ¨¡ôïÇÉ%˛\"w4å•\n\$¯…zV¢SQD€:›6ù´‰GãwM‘ÓS0Bâ-s∆Í)„æZÌ§c|À^RöÔEË8kMÔ—Ãsådπkaô)h%\"P‡ç0nn˜Ü/¡ö#;û÷g\rd»∏8ÜﬁF<3\$©,ÂP);<4`Œ¢<2\nî ıÈ@w-Æ·ÕóAœ0π∫™ìπLrÓYhÏXC‡aò>∫Êtã∫LıÏ2Çyto;2á›Q™±tÓ frmË:ßîAÌ˘â°˜AN∫›\\\"k∫5oVÎ…É=Ó¿tÖ7r1›p‰Av\\+û9™Ñ‚Ä{∞Á^(iúâf¨=∑rä“∫äu⁄ ˚tÿ]y”ﬁÖ–˘Cˆ∂∫¡≥“ı›‹gi•vfÅ›˘+•√ò| Ï;úÄ∏¬‡]è~” |\re˜•Ïøìö›Ç⁄'ÉÌ˚≤âî¶‰Ø≤∞	Ω\0+Wáçcoµw6wd Suºj®3@ñåÚ0!„˜\n .wÄm[8x<≤ÀcM¨\n9˝≤˝'a˘ﬁà1>»£í[∂Ôµ˙dÔﬁuxØ‡<\"Yéc∏ﬁB!iπ•Íïw¿}íÙ5Uπk∫∫ç‹ÿ]≠∂∏‘“¿{ÛI◊öRÖâñ•=f W~Ê]…(beaÆ'ubÔmë>É)\$∞ÜP˜·-öÉ6˛êR*IGu#∆ïUKµAXåt—(”`_¬‡\"†æ£p∏ &UÀÀŸIÌ…]˝¡YG6Pù]Ar!b° *–ôJäoïµ”ØÂˇôÛÅÔ¡Úv˝Ω*¿†ÿ!Èö~_™¿Ÿ4Bçê≥_~RBòiK˘åí˛`Áâ&J€\0≠ÙÆN\0–\$‡Ãè˛ÂC¬K úS–Ú‚jZ§–†Ã˚0pvMJ†bN`LˇÊ≠e∫/`RO.0P‰82`Í	Â¸∆∏d ¬òGx«bPû-(@…∏”@Ê4®H%<&ñ¿ÃZ‡ô¿ËpÑ¨∞ä%\0ÆpÄ––Ñ¯Í„	ÖØ	‡»/\"ˆ¢J≥¢\nsÜñ_¿Ã\rå‡gé`ãú!k‰pX	Ë–:ƒvÌÁ6p\$˙'«•RUeZˇ®d\$Ï\nL·B∫‚ÜÛ.ﬁdånÄÓ§“tmÄ>vÖj‰ïÌÄ)ë	M∫\r\0¬.‡ äHí—\"Ö5Ç*!e∫ZJ∫âËíÎ„f(dc±º(x‹—jg\0\\ıÄ¬ı¿∂ Z@∫‡Í|`^õèr)<ã(íàÑàÜ»)ÃÎ™Û –Ï@Yk¬mÃÌl3Qy—Å@…ëå—êfŒÏPnÑÁº®–T†ÚØçN∑mR’q≥Ì‚Vmv˙N÷çÇ|˙–®Z≤Ñ»Ü⁄(Yp¯â\"Ñ4«®Ê‡Ú&ÄÓ%çl“P`ƒÄ£Xx bbd–r0Fr5∞<ªCÊ≤z®ÅØ6‰he!§à\rdzê‡ÿK;ƒt≥≤\nŸÕ†ÖH∆ãQö\$QüEnn¢n\r¿ö©#öT\$∞≤Àà(»ü—©|c§,º-˙#Ë⁄\r†‹·âJµ{d—E\n\$≤∆BrúiT‘Úë+≈2PEDïBeã}&%Rf≤•\n¸É^ÙàC‡»Z‡Z RVì≈A,—;ë´Á<¬ƒÏ\0O1È‘Íc^\r%Ç\r ÏÎ`“n\0y1Ë‘.¬\r¥ƒÇK1ÊM3HÆ\r\"˚0\0NkXèPr∏Ø{3 Ï}	\nS»dÜà⁄óäx.ZÒRTÒÑíwS;53 .¢s4sO3F∫Ÿ2èS~YFpZs°'Œ@ŸëOqR4\n≠6q6@DhŸ6Õ’7vE¢l\"≈^;-Â(¬&œb*≤*ãÚ.! ‰\rí!#Áx'G\"ÄÕÜwâ¡\"˙’ »2!\"R(v¿XåÊ|\"DÃv¿¶)@·,∏zmÚAÕwT@¿‘  –\nÇ÷”∫–´h–¥ID‘P\$m>Ê\r&`á>¥4»“A#*Î#í<îw\$T{\$¥4@õàd”¥Rem6Ø-#Ddæ%E•DT\\†\$)@‹¥WC¨(tÆ\"M‡‹#@˙TFü\r,g¶\rP8√~ë¥÷£J¸∞c†ˆå‡ƒπ∆ÇÍ  é\"ôL™Z‘‰\r+P4˝=•§ôS‚ôTıA)é0\"¶CDh«M\nû%F‘p÷”¸|éfLNlFtDmHØ™˛∞5Â=HÕ\nõéƒº4¸≥ı\$‡æKÒ6\rbZ‡®\r\"pEQ%§wJ¥ˇV0‘íM%Âl\"hÅPFÔA¨·A„åÆÚ/Gí6†h6]5•\$ÄfãS˜CLiRT?R®û˛CñÒı£HUßZ§ÊYbF˛/Ê.ÍZ‹\"\"^Œy¥6RîG ≤ãÃn‚˙‹åç\$™—Â\\&O÷(v^ œKU∫—ÆŒ“am≥(\rÄäÔ∫Øæ¸\$_™Ê%Ò+KTtÿˆ.Ÿñ36\nÎcµî:¥@6 ˙jP√AQıFí/SÆk\"<4AÑgA–aUÖ\$'Îà”·f‡˚QO\"◊k~≤S;≈¿ΩÛ.ÔÀ:†àkëº9≠¸≤äÛe]`n˙º“-7®ò;Óﬂ+VÀ‚8W¿©2H¢UãÆYlBÌvﬁˆ‚Øé÷‘Ü¥∞∂ˆ	ß˝‚ÓpÆ÷…læm\0Ò4BÚ)•X¡\0 ¬QﬂqFSqó4ñˇnFx+p‘Ú¶E∆Sov˙GW7o◊w◊KRW◊\r4`|cqÓe7,◊19∑u†œu˜cq‰í\"LC†t¿h‚)ß\rÄ‡J¿\\¯W@‡	Á|D#S\rü%å5lÊ!%+ì+Â^ák^ ô`/û7∏â(z*ÒòãÄì¥EÄ›{¶S(W‡◊-ìXƒó0V£ë0À•óÓ»=ÓÕa	~ÎfBÎÀï2Q≠Í¬ru mC¬ÏÎÑ£tér(\0Q!K;xN˝W¿˙ˇß¯»?b<†@≈`÷X,∫á`0e∫∆ÇN'≤¬ëÖöú§&~ë¯tî”uá\"| ¨iÖ ÒBÂ† 7æR¯î ∏õlSuÜ∞8A˚âdF%(‘˙†‰˙ÔûÛ?3@A-oQä≈∫@|~©KÜ¿ ^@xÛçböú~úD¶@ÿ≥âò∏õÖTN≈ZÄCç	Wà“¬ix<\0P|ƒÊ\n\0û\n`®•†éπ\"&?st|√ØàwÓ%Öà‡ËmdÍu¿N£^8¿[t©9É™B\$‡ßé©¶'\">Uå~ˇ98á†ÈìÚ√îFƒf ∞πÄuÄ»∞û/)9á¿ôà\0·òÎA˘z\"FWAx§\$'©jG¥(\"Ÿ ±s%TèíHäÓﬂ¿e,	Mú7Ôãbº «ÖÿaÑ Àìî∆É∑&wY‘œÜ3ò∞ÿ¯ /í\rœñ˘ØüûŸ{õ\"˘›úp{%4bÑÛå`Ìå§‘ı~nÄÂçE3	ïŒ†õç∞9éÂ3X÷dõé‰’èZû≈9Ô'öô@á®áëlªfØıùÿQèbP§*GÖoäÂ≈`8ï®ëØû˘AõÊB|¿z	@¶	‡ùb°Zn_Õh∫'—¢F\$f¨èß`ˆÛ∫ÜHdDdåH%4\rsŒAjLR»'ﬁ˘f⁄9g Iœÿ,R\\∑¯î >\nÜöH[¥\"ç∞¿Ó©™\r”ÅÖå¬ïLÃ,%ÎFLl8gzLÁ<0kùo\$«k≠·`“√KP‘vÂ@dœ'Vê:VîÿM¸%±Ë’@¯6«<\r‡˘T´ãÆLE¥âN‘ÄS#ˆ.∂[Ñx4æaÁÃ≠¥LLÇÆ†™\n@í£\0€´tŸ≤Â\n^F≠óù∫•∫ä5`Õ Rèì7»lL†uµ(ôÅdí∫°π ‘\r‰Bf/uCf◊4ˇc“û BÔÏÄ_¥nL‘\0© \$ªÓaY∆¶∂∏Ä~¿UkÔv•eÙÀ•¶À≤\0ôZíaZóìöúXÿ£¶û|Cäqì®/<}ÿ≥°ñ≈√∫≤î∫∂ Z∫û*≠w\nO„á≈z`º5ìÆ18∂c¯ôÄ˚ÆØ≠ÆÊ⁄I¿Q2Ys«KãòêÄÊ\n£\\õû\"õ≠ √∞ácÜÚ*ıB∂ÄÓÃ.ÈR1<3+ı≈µ*ÿSÈ[ı4”mÏ≠õ:RÅhãëITdevŒIµH‰Ë“-Zw\\∆%nË56å\nÃW”iè\$’≈çow¨ò+©†∫˘Àr…∂&Jq+˚}“D‡¯º”j´êd≈Œ?ÊU%BBe«/MÇ∂Nm=œÑÛU∑¬b\$HRf™wb|ï≤x d˚2ÊNiS‡Ûÿg…@Óq@úﬂ>ŒSv†ÑçßóïÉ|ÔkråxΩå\0{‘RÉ=FˇœŒŒ‚Æœ#rΩÇ8	àZ‡v»8* ≥£{2S›+;S¶úÇ”®∆+yL\$\"_€Î©BÁ8¨›\"E∏%∫çÅ‡∫å\n¯ë–¬pæp''´pÇÛwU“™\"8–±I\\ @çÖ† æ áLnéÊ†Rﬂ#M‰Dµ˛qûLN∆Ó\n\\èíÃé\$`~@`\0uÁâ~^@‡’là-{5Ò,@bru¡o[¡≤æ®’}È/Òy.◊È {È6qÇ∞Rôp‡–\$∏+1é3€˙⁄˙+É®O!D)ÖÆ†‡\nuî<çØ,´·Òﬂ=ÇJd∆+}Åµd#©0…ûc”ê3U3ªEYπ˚¢\r˚¶tj5“•7ªe©òw◊Ñ«°˙µ¢^çÇqﬂÇø9∆<\$}kÌÕÚåRI-¯∞∏+'_Ne?S€RÌhd*Xò4ÈÆ¸c}¨Ë\"@äàvi>;5>Dnâ ò\r‰Î)bNÈuP@Y‰G<Ò®6iı#PB2AΩ-Ì0d0+Ö¸gK˚¯øÌ?®nÈ„¸dúd¯O¿ÇåØÂ·c¸i<ã˙ëã0\0ú\\˘óÎÅ—gÓ¶ê˘ÊÍ°ññÖNTi'††∑Ù;iÙmj·ê‹à≈˜ªç∏uŒJ+™V~¿≤˘†'ol`˘≥øÛ\",¸ùÜÃ£◊”F¿Âñ	˝‚{C©∏è§˛T aœNE€ÉQ∆p¥ pÅÄ+?¯\n∆>Ñ'lΩ§* t…KŒ¨p∞(YC\n-qÃî0Â\"*…ï¡,#¸‚˜7∫Å\"%®+qƒ∏ÍB±∞=Âi.@êx7:≈%GcYI–à0*ôÓ√êk¿€àÑ\\á∑ØQ_{§†≈«#¡˝\rÁ{H≥[p® >7”chÎnçŒ¬‘.úµ£¶S|&JÚM«æ8¥¿mÄOh˛ƒÌ	’—qJ&êaÄ›¢®'â.bÁOpÿÏ\$ˆñ≠‹ÄD@∞CÇHBñ	É»&‚›°|\$‘¨-6∞≤+Ã+¬å Üï¬‡úp∫Ö‡¨°AC\rí…ìÖÏ/Œ0¥Ò¬Ó¢MÜ√iZänEúÕ¢j*>ô˚!“¢u%§©gÿ0£‡Ä@‰ø5}rÖ…+3ú%¬î-mã¢GÇ<î„•T;0∞Ø®íÜDV£d¿g€9'lM∂˝Hà£ F@‰Pòãun¸tFB%¥Mƒt'‰G‘2≈¿@2¢<´eôî;¢`àı=LXƒ2‡œ‰Xª}oc.Lä+‚x”éÜ&D®aíÄ°Ä…´¡F2\ngLêEÉ∞.\\xSL˝x≠;lw—D=0_QV,a 5ä+LÈÛ+€|\$≈i≠jZ\nÍóD÷EŒ,Bæt\\œ'H0¡åê±R~(\\\"¢÷:î–n*˚ö’(°◊oÆ1w„’QÌ◊rˆ“√Eète”FïÖ\$ËS—í]–\rL‰yFÑâë\\Båi¿hêîhd·ˇ&·öáh;foõæB-y`≈‘0àÑJélPÈxao∑\$äXqº,(÷°ÜC*	«\"ÇÉî§\"Aë¿ÛàüE\nˇ”æG Ø-zlé†í„ƒî∞ÄÅï∞œé—»„Œî≈^!¡˘‚^sU¬J¸ÉD Ùu√\n1¢`ü≤ÊÑ¡ÙWÀ˙D*u% ÚΩ^ÎË<Åë; ÿ8iˇÑ|`_›◊…¢B\rÎ`\nF<08F‰Lrdà¸≈1OHoc¡ê⁄oƒXäewCAê{Lâ\$L˘ÿ\næö@21W¿›–Ê9—œâD'–AîÅ%#…ª—(Ò\n]ﬁìÙÓ5BHƒcmQWØS¯HÕÇ¢¨r\\Öﬂ ›‚‘@‚k[)ªÑ¨M¬©?ù-q#9Ë? •y1¯;y·pbıH/m1ˇ#TÇWÕ#–ªª|–Qƒ1‚ÊiTäπg©Ñíêà©‰˛ 9Ÿ\"Mîã≥U¡\"˘∞TŸJ\0˘\\- â,H-a“0Fõ∂¯˜»ã,£Bg]&g	0.GakîÉb¶q–I„zXB÷lœ¢Ä");}elseif($_GET["file"]=="jush.js"){header("Content-Type: text/javascript; charset=utf-8");echo
lzw_decompress('');}elseif($_GET["file"]=="logo.png"){header("Content-Type: image/png");echo"âPNG\r\n\n\0\0\0\rIHDR\0\0\09\0\0\09\0\0\0~6û∂\0\0\0000PLTE\0\0\0Éó≠+NvYtìsâ£ûÆæ¥æÃ»“⁄¸çë¸su¸IJ˜”‘¸/.¸¸Ø±˙¸˙C•◊\0\0\0tRNS\0@Êÿf\0\0\0	pHYs\0\0\0\0\0öú\0\0¥IDAT8ç’îÕN¬@«˚E·Ïlœ∂ı§p6àG.\$=£•«>Å·	w5r}Çz7≤>ÄëPÂ#\$å≥K°j´7ç¸›∂øÃŒÃ?4mïÑà—˜t&Ó~¿3!0ì0äö^ÑΩAf0ﬁ\"ÂΩÌ, *†Á4ºå‚o•EË≥Ë◊X(*Y”Ûº∏	6	ÔPcOW¢…Œ‹ämí¨rÉ0√~/†·L®\rXj#÷m ¡˙j¿CÄ]G¶mÊ\0∂}ﬁÀ¨ﬂëuºA9¿X£\n‘ÿ8ºV±Yƒ+«D#®iqﬁnKQ8J‡1Q6≤ÊY0ß`ïüP≥bQç\\hî~>Û:pS…Ä£¶º¢ÿÛGEıQ=ÓIœ{í*ü3Î2£7˜\ne LËBä~–/R(\$∞) Áã ó¡HQnÄiï6J∂	<ù◊-.ñw«…™jÍVm´Í¸mø?SﬁH†õv√Ã˚Ò∆©ß›\0‡÷^’q´∂)™ó€]˜ãUπ92—,;ˇ«çÓ'p¯µ£!XÀÉ‰⁄‹ˇLÒD.ªt√¶ó˝/w√”‰ÏR˜ù	w≠d”÷r2Ô∆§™4[=ΩE5˜S+Òóc\0\0\0\0IENDÆB`Ç";}exit;}if($_GET["script"]=="version"){$l=get_temp_dir()."/adminer.version";@unlink($l);$n=file_open_lock($l);if($n)file_write_unlock($n,serialize(array("signature"=>$_POST["signature"],"version"=>$_POST["version"])));exit;}if(!$_SERVER["REQUEST_URI"])$_SERVER["REQUEST_URI"]=$_SERVER["ORIG_PATH_INFO"];if(!strpos($_SERVER["REQUEST_URI"],'?')&&$_SERVER["QUERY_STRING"]!="")$_SERVER["REQUEST_URI"].="?$_SERVER[QUERY_STRING]";if($_SERVER["HTTP_X_FORWARDED_PREFIX"])$_SERVER["REQUEST_URI"]=$_SERVER["HTTP_X_FORWARDED_PREFIX"].$_SERVER["REQUEST_URI"];define('Adminer\HTTPS',($_SERVER["HTTPS"]&&strcasecmp($_SERVER["HTTPS"],"off"))||ini_bool("session.cookie_secure"));@ini_set("session.use_trans_sid",'0');if(!defined("SID")){session_cache_limiter("");session_name("adminer_sid");session_set_cookie_params(0,preg_replace('~\?.*~','',$_SERVER["REQUEST_URI"]),"",HTTPS,true);session_start();}remove_slashes(array(&$_GET,&$_POST,&$_COOKIE),$Xb);if(function_exists("get_magic_quotes_runtime")&&get_magic_quotes_runtime())set_magic_quotes_runtime(false);@set_time_limit(0);@ini_set("precision",'15');function
lang($s,$Kd=null){$ia=func_get_args();$ia[0]=$s;return
call_user_func_array('Adminer\lang_format',$ia);}function
lang_format($Af,$Kd=null){if(is_array($Af)){$le=($Kd==1?0:1);$Af=$Af[$le];}$Af=str_replace("'",'‚Äô',$Af);$ia=func_get_args();array_shift($ia);$hc=str_replace("%d","%s",$Af);if($hc!=$Af)$ia[0]=format_number($Kd);return
vsprintf($hc,$ia);}define('Adminer\LANG','en');abstract
class
SqlDb{static$instance;var$extension;var$flavor='';var$server_info;var$affected_rows=0;var$info='';var$errno=0;var$error='';protected$multi;abstract
function
attach($N,$U,$F);abstract
function
quote($P);abstract
function
select_db($gb);abstract
function
query($H,$Ff=false);function
multi_query($H){return$this->multi=$this->query($H);}function
store_result(){return$this->multi;}function
next_result(){return
false;}}if(extension_loaded('pdo')){abstract
class
PdoDb
extends
SqlDb{protected$pdo;function
dsn($ub,$U,$F,array$C=array()){$C[\PDO::ATTR_ERRMODE]=\PDO::ERRMODE_SILENT;$C[\PDO::ATTR_STATEMENT_CLASS]=array('Adminer\PdoResult');try{$this->pdo=new
\PDO($ub,$U,$F,$C);}catch(\Exception$Kb){return$Kb->getMessage();}$this->server_info=@$this->pdo->getAttribute(\PDO::ATTR_SERVER_VERSION);return'';}function
quote($P){return$this->pdo->quote($P);}function
query($H,$Ff=false){$I=$this->pdo->query($H);$this->error="";if(!$I){list(,$this->errno,$this->error)=$this->pdo->errorInfo();if(!$this->error)$this->error='Unknown error.';return
false;}$this->store_result($I);return$I;}function
store_result($I=null){if(!$I){$I=$this->multi;if(!$I)return
false;}if($I->columnCount()){$I->num_rows=$I->rowCount();return$I;}$this->affected_rows=$I->rowCount();return
true;}function
next_result(){$I=$this->multi;if(!is_object($I))return
false;$I->_offset=0;return@$I->nextRowset();}}class
PdoResult
extends
\PDOStatement{var$_offset=0,$num_rows;function
fetch_assoc(){return$this->fetch_array(\PDO::FETCH_ASSOC);}function
fetch_row(){return$this->fetch_array(\PDO::FETCH_NUM);}private
function
fetch_array($Ad){$J=$this->fetch($Ad);return($J?array_map(array($this,'unresource'),$J):$J);}private
function
unresource($W){return(is_resource($W)?stream_get_contents($W):$W);}function
fetch_field(){$K=(object)$this->getColumnMeta($this->_offset++);$T=$K->pdo_type;$K->type=($T==\PDO::PARAM_INT?0:15);$K->charsetnr=($T==\PDO::PARAM_LOB||(isset($K->flags)&&in_array("blob",(array)$K->flags))?63:0);return$K;}function
seek($Ld){for($q=0;$q<$Ld;$q++)$this->fetch();}}}function
add_driver($r,$B){SqlDriver::$drivers[$r]=$B;}function
get_driver($r){return
SqlDriver::$drivers[$r];}abstract
class
SqlDriver{static$instance;static$drivers=array();static$extensions=array();static$jush;protected$conn;protected$types=array();var$insertFunctions=array();var$editFunctions=array();var$unsigned=array();var$operators=array();var$functions=array();var$grouping=array();var$onActions="RESTRICT|NO ACTION|CASCADE|SET NULL|SET DEFAULT";var$partitionBy=array();var$inout="IN|OUT|INOUT";var$enumLength="'(?:''|[^'\\\\]|\\\\.)*'";var$generated=array();static
function
connect($N,$U,$F){$e=new
Db;return($e->attach($N,$U,$F)?:$e);}function
__construct(Db$e){$this->conn=$e;}function
types(){return
call_user_func_array('array_merge',array_values($this->types));}function
structuredTypes(){return
array_map('array_keys',$this->types);}function
enumLength(array$j){}function
unconvertFunction(array$j){}function
select($Q,array$M,array$Z,array$p,array$D=array(),$w=1,$E=0,$qe=false){$Tc=(count($p)<count($M));$H=adminer()->selectQueryBuild($M,$Z,$p,$D,$w,$E);if(!$H)$H="SELECT".limit(($_GET["page"]!="last"&&$w&&$p&&$Tc&&JUSH=="sql"?"SQL_CALC_FOUND_ROWS ":"").implode(", ",$M)."\nFROM ".table($Q),($Z?"\nWHERE ".implode(" AND ",$Z):"").($p&&$Tc?"\nGROUP BY ".implode(", ",$p):"").($D?"\nORDER BY ".implode(", ",$D):""),$w,($E?$w*$E:0),"\n");$df=microtime(true);$J=$this->conn->query($H);if($qe)echo
adminer()->selectQuery($H,$df,!$J);return$J;}function
delete($Q,$we,$w=0){$H="FROM ".table($Q);return
queries("DELETE".($w?limit1($Q,$H,$we):" $H$we"));}function
update($Q,array$O,$we,$w=0,$Pe="\n"){$Uf=array();foreach($O
as$v=>$W)$Uf[]="$v = $W";$H=table($Q)." SET$Pe".implode(",$Pe",$Uf);return
queries("UPDATE".($w?limit1($Q,$H,$we,$Pe):" $H$we"));}function
insert($Q,array$O){return
queries("INSERT INTO ".table($Q).($O?" (".implode(", ",array_keys($O)).")\nVALUES (".implode(", ",$O).")":" DEFAULT VALUES").$this->insertReturning($Q));}function
insertReturning($Q){return"";}function
insertUpdate($Q,array$L,array$pe){return
false;}function
begin(){return
queries("BEGIN");}function
commit(){return
queries("COMMIT");}function
rollback(){return
queries("ROLLBACK");}function
slowQuery($H,$sf){}function
convertSearch($s,array$W,array$j){return$s;}function
value($W,array$j){return(method_exists($this->conn,'value')?$this->conn->value($W,$j):$W);}function
quoteBinary($He){return
q($He);}function
warnings(){}function
tableHelp($B,$Wc=false){}function
inheritsFrom($Q){return
array();}function
inheritedTables($Q){return
array();}function
partitionsInfo($Q){return
array();}function
hasCStyleEscapes(){return
false;}function
engines(){return
array();}function
supportsIndex(array$R){return!is_view($R);}function
indexAlgorithms(array$lf){return
array();}function
checkConstraints($Q){return
get_key_vals("SELECT c.CONSTRAINT_NAME, CHECK_CLAUSE
FROM INFORMATION_SCHEMA.CHECK_CONSTRAINTS c
JOIN INFORMATION_SCHEMA.TABLE_CONSTRAINTS t ON c.CONSTRAINT_SCHEMA = t.CONSTRAINT_SCHEMA AND c.CONSTRAINT_NAME = t.CONSTRAINT_NAME
WHERE c.CONSTRAINT_SCHEMA = ".q($_GET["ns"]!=""?$_GET["ns"]:DB)."
AND t.TABLE_NAME = ".q($Q)."
AND CHECK_CLAUSE NOT LIKE '% IS NOT NULL'",$this->conn);}function
allFields(){$J=array();if(DB!=""){foreach(get_rows("SELECT TABLE_NAME AS tab, COLUMN_NAME AS field, IS_NULLABLE AS nullable, DATA_TYPE AS type, CHARACTER_MAXIMUM_LENGTH AS length".(JUSH=='sql'?", COLUMN_KEY = 'PRI' AS `primary`":"")."
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = ".q($_GET["ns"]!=""?$_GET["ns"]:DB)."
ORDER BY TABLE_NAME, ORDINAL_POSITION",$this->conn)as$K){$K["null"]=($K["nullable"]=="YES");$J[$K["tab"]][]=$K;}}return$J;}}class
Adminer{static$instance;var$error='';private$values=array();function
name(){return"<a href='https://www.adminer.org/editor/'".target_blank()." id='h1'><img src='".h(preg_replace("~\\?.*~","",ME)."?file=logo.png&version=5.4.1")."' width='24' height='24' alt='' id='logo'>".'Editor'."</a>";}function
credentials(){return
array(SERVER,$_GET["username"],get_password());}function
connectSsl(){}function
permanentLogin($Ya=false){return
password_file($Ya);}function
bruteForceKey(){return$_SERVER["REMOTE_ADDR"];}function
serverName($N){}function
database(){if(connection()){$hb=adminer()->databases(false);return(!$hb?get_val("SELECT SUBSTRING_INDEX(CURRENT_USER, '@', 1)"):$hb[(information_schema($hb[0])?1:0)]);}}function
operators(){return
array("<=",">=");}function
schemas(){return
schemas();}function
databases($ac=true){return
get_databases($ac);}function
pluginsLinks(){}function
queryTimeout(){return
5;}function
afterConnect(){}function
headers(){}function
csp($bb){return$bb;}function
head($eb=null){return
true;}function
bodyClass(){echo" editor";}function
css(){$J=array();foreach(array("","-dark")as$Ad){$l="adminer$Ad.css";if(file_exists($l)){$Vb=file_get_contents($l);$J["$l?v=".crc32($Vb)]=($Ad?"dark":(preg_match('~prefers-color-scheme:\s*dark~',$Vb)?'':'light'));}}return$J;}function
loginForm(){echo"<table class='layout'>\n",adminer()->loginFormField('username','<tr><th>'.'Username'.'<td>',input_hidden("auth[driver]","server").'<input name="auth[username]" autofocus value="'.h($_GET["username"]).'" autocomplete="username" autocapitalize="off">'),adminer()->loginFormField('password','<tr><th>'.'Password'.'<td>','<input type="password" name="auth[password]" autocomplete="current-password">'),"</table>\n","<p><input type='submit' value='".'Login'."'>\n",checkbox("auth[permanent]",1,$_COOKIE["adminer_permanent"],'Permanent login')."\n";}function
loginFormField($B,$xc,$X){return$xc.$X."\n";}function
login($kd,$F){return
true;}function
tableName($lf){return
h(isset($lf["Engine"])?($lf["Comment"]!=""?$lf["Comment"]:$lf["Name"]):"");}function
fieldName($j,$D=0){return
h(preg_replace('~\s+\[.*\]$~','',($j["comment"]!=""?$j["comment"]:$j["field"])));}function
selectLinks($lf,$O=""){$a=$lf["Name"];if($O!==null)echo'<p class="tabs"><a href="'.h(ME.'edit='.urlencode($a).$O).'">'.'New item'."</a>\n";}function
foreignKeys($Q){return
foreign_keys($Q);}function
backwardKeys($Q,$kf){$J=array();foreach(get_rows("SELECT TABLE_NAME, CONSTRAINT_NAME, COLUMN_NAME, REFERENCED_COLUMN_NAME
FROM information_schema.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = ".q(adminer()->database())."
AND REFERENCED_TABLE_SCHEMA = ".q(adminer()->database())."
AND REFERENCED_TABLE_NAME = ".q($Q)."
ORDER BY ORDINAL_POSITION",null,"")as$K)$J[$K["TABLE_NAME"]]["keys"][$K["CONSTRAINT_NAME"]][$K["COLUMN_NAME"]]=$K["REFERENCED_COLUMN_NAME"];foreach($J
as$v=>$W){$B=adminer()->tableName(table_status1($v,true));if($B!=""){$Je=preg_quote($kf);$Pe="(:|\\s*-)?\\s+";$J[$v]["name"]=(preg_match("(^$Je$Pe(.+)|^(.+?)$Pe$Je\$)iu",$B,$z)?$z[2].$z[3]:$B);}else
unset($J[$v]);}return$J;}function
backwardKeysPrint($ta,$K){foreach($ta
as$Q=>$sa){foreach($sa["keys"]as$Pa){$x=ME.'select='.urlencode($Q);$q=0;foreach($Pa
as$c=>$W)$x
.=where_link($q++,$c,$K[$W]);echo"<a href='".h($x)."'>".h($sa["name"])."</a>";$x=ME.'edit='.urlencode($Q);foreach($Pa
as$c=>$W)$x
.="&set".urlencode("[".bracket_escape($c)."]")."=".urlencode($K[$W]);echo"<a href='".h($x)."' title='".'New item'."'>+</a> ";}}}function
selectQuery($H,$df,$Rb=false){return"<!--\n".str_replace("--","--><!-- ",$H)."\n(".format_time($df).")\n-->\n";}function
rowDescription($Q){foreach(fields($Q)as$j){if(preg_match("~varchar|character varying~",$j["type"]))return
idf_escape($j["field"]);}return"";}function
rowDescriptions($L,$ec){$J=$L;foreach($L[0]as$v=>$W){if(list($Q,$r,$B)=$this->_foreignColumn($ec,$v)){$Fc=array();foreach($L
as$K)$Fc[$K[$v]]=q($K[$v]);$nb=$this->values[$Q];if(!$nb)$nb=get_key_vals("SELECT $r, $B FROM ".table($Q)." WHERE $r IN (".implode(", ",$Fc).")");foreach($L
as$A=>$K){if(isset($K[$v]))$J[$A][$v]=(string)$nb[$K[$v]];}}}return$J;}function
selectLink($W,$j){}function
selectVal($W,$x,$j,$Wd){$J="$W";$x=h($x);if(is_blob($j)&&!is_utf8($W)){$J=lang_format(array('%d byte','%d bytes'),strlen($Wd));if(preg_match("~^(GIF|\xFF\xD8\xFF|\x89PNG\x0D\x0A\x1A\x0A)~",$Wd))$J="<img src='$x' alt='$J'>";}if(like_bool($j)&&$J!="")$J=(preg_match('~^(1|t|true|y|yes|on)$~i',$W)?'yes':'no');if($x)$J="<a href='$x'".(is_url($x)?target_blank():"").">$J</a>";if(preg_match('~date~',$j["type"]))$J="<div class='datetime'>$J</div>";return$J;}function
editVal($W,$j){if(preg_match('~date|timestamp~',$j["type"])&&$W!==null)return
preg_replace('~^(\d{2}(\d+))-(0?(\d+))-(0?(\d+))~','$1-$3-$5',$W);return$W;}function
config(){return
array();}function
selectColumnsPrint($M,$d){}function
selectSearchPrint($Z,$d,$u){$Z=(array)$_GET["where"];echo'<fieldset id="fieldset-search"><legend>'.'Search'."</legend><div>\n";$Zc=array();foreach($Z
as$v=>$W)$Zc[$W["col"]]=$v;$q=0;$k=fields($_GET["select"]);foreach($d
as$B=>$mb){$j=$k[$B];if($j["type"]=="enum"||like_bool($j)){$v=$Zc[$B];$q--;echo"<div>".h($mb).":".input_hidden("where[$q][col]",$B);$W=idx($Z[$v],"val");echo(like_bool($j)?"<select name='where[$q][val]'>".optionlist(array(""=>"",'no','yes'),$W,true)."</select>":enum_input("checkbox"," name='where[$q][val][]'",$j,(array)$W,'empty')),"</div>\n";unset($d[$B]);}elseif(is_array($C=$this->foreignKeyOptions($_GET["select"],$B))){if($k[$B]["null"])$C[0]='('.'empty'.')';$v=$Zc[$B];$q--;echo"<div>".h($mb).input_hidden("where[$q][col]",$B).input_hidden("where[$q][op]","=").": <select name='where[$q][val]'>".optionlist($C,idx($Z[$v],"val"),true)."</select></div>\n";unset($d[$B]);}}$q=0;foreach($Z
as$W){if(($W["col"]==""||$d[$W["col"]])&&"$W[col]$W[val]"!=""){echo"<div><select name='where[$q][col]'><option value=''>(".'anywhere'.")".optionlist($d,$W["col"],true)."</select>",html_select("where[$q][op]",array(-1=>"")+adminer()->operators(),$W["op"]),"<input type='search' name='where[$q][val]' value='".h($W["val"])."'>".script("mixin(qsl('input'), {onkeydown: selectSearchKeydown, onsearch: selectSearchSearch});","")."</div>\n";$q++;}}echo"<div><select name='where[$q][col]'><option value=''>(".'anywhere'.")".optionlist($d,null,true)."</select>",script("qsl('select').onchange = selectAddRow;",""),html_select("where[$q][op]",array(-1=>"")+adminer()->operators()),"<input type='search' name='where[$q][val]'></div>",script("mixin(qsl('input'), {onchange: function () { this.parentNode.firstChild.onchange(); }, onsearch: selectSearchSearch});"),"</div></fieldset>\n";}function
selectOrderPrint($D,$d,$u){$Vd=array();foreach($u
as$v=>$t){$D=array();foreach($t["columns"]as$W)$D[]=$d[$W];if(count(array_filter($D,'strlen'))>1&&$v!="PRIMARY")$Vd[$v]=implode(", ",$D);}if($Vd)echo'<fieldset><legend>'.'Sort'."</legend><div>","<select name='index_order'>".optionlist(array(""=>"")+$Vd,(idx($_GET["order"],0)!=""?"":$_GET["index_order"]),true)."</select>","</div></fieldset>\n";if($_GET["order"])echo"<div style='display: none;'>".hidden_fields(array("order"=>array(1=>reset($_GET["order"])),"desc"=>($_GET["desc"]?array(1=>1):array()),))."</div>\n";}function
selectLimitPrint($w){echo"<fieldset><legend>".'Limit'."</legend><div>",html_select("limit",array("",50,100),$w),"</div></fieldset>\n";}function
selectLengthPrint($pf){}function
selectActionPrint($u){echo"<fieldset><legend>".'Action'."</legend><div>","<input type='submit' value='".'Select'."'>","</div></fieldset>\n";}function
selectCommandPrint(){return
true;}function
selectImportPrint(){return
true;}function
selectEmailPrint($_b,$d){}function
selectColumnsProcess($d,$u){return
array(array(),array());}function
selectSearchProcess($k,$u){$J=array();foreach((array)$_GET["where"]as$v=>$Z){$Ma=$Z["col"];$Qd=$Z["op"];$W=$Z["val"];if(($v>=0&&$Ma!="")||$W!=""){$Sa=array();foreach(($Ma!=""?array($Ma=>$k[$Ma]):$k)as$B=>$j){if($Ma!=""||is_numeric($W)||!preg_match(number_type(),$j["type"])){$B=idf_escape($B);if($Ma!=""&&$j["type"]=="enum"){$Hc=array();foreach($W
as$Tf){if(preg_match('~val-~',$Tf))$Hc[]=q(substr($Tf,4));}$Sa[]=(in_array("null",$W)?"$B IS NULL OR ":"").($Hc?"$B IN (".implode(", ",$Hc).")":"0");}else{$qf=preg_match('~char|text|enum|set~',$j["type"]);$X=adminer()->processInput($j,(!$Qd&&$qf&&preg_match('~^[^%]+$~',$W)?"%$W%":$W));$Sa[]=driver()->convertSearch($B,$Z,$j).($X=="NULL"?" IS".($Qd==">="?" NOT":"")." $X":(in_array($Qd,adminer()->operators())||$Qd=="="?" $Qd $X":($qf?" LIKE $X":" IN (".($X[0]=="'"?str_replace(",","', '",$X):$X).")")));if($v<0&&$W=="0")$Sa[]="$B IS NULL";}}}$J[]=($Sa?"(".implode(" OR ",$Sa).")":"1 = 0");}}return$J;}function
selectOrderProcess($k,$u){$Jc=$_GET["index_order"];if($Jc!="")unset($_GET["order"][1]);if($_GET["order"])return
array(idf_escape(reset($_GET["order"])).($_GET["desc"]?" DESC":""));foreach(($Jc!=""?array($u[$Jc]):$u)as$t){if($Jc!=""||$t["type"]=="INDEX"){$sc=array_filter($t["descs"]);$mb=false;foreach($t["columns"]as$W){if(preg_match('~date|timestamp~',$k[$W]["type"])){$mb=true;break;}}$J=array();foreach($t["columns"]as$v=>$W)$J[]=idf_escape($W).(($sc?$t["descs"][$v]:$mb)?" DESC":"");return$J;}}return
array();}function
selectLimitProcess(){return(isset($_GET["limit"])?intval($_GET["limit"]):50);}function
selectLengthProcess(){return"100";}function
selectEmailProcess($Z,$ec){return
false;}function
selectQueryBuild($M,$Z,$p,$D,$w,$E){return"";}function
messageQuery($H,$rf,$Rb=false){return" <span class='time'>".@date("H:i:s")."</span><!--\n".str_replace("--","--><!-- ",$H)."\n".($rf?"($rf)\n":"")."-->";}function
editRowPrint($Q,$k,$K,$Mf){}function
editFunctions($j){$J=array();if($j["null"]&&preg_match('~blob~',$j["type"]))$J["NULL"]='empty';$J[""]=($j["null"]||$j["auto_increment"]||like_bool($j)?"":"*");if(preg_match('~date|time~',$j["type"]))$J["now"]='now';if(preg_match('~_(md5|sha1)$~i',$j["field"],$z))$J[]=strtolower($z[1]);return$J;}function
editInput($Q,$j,$b,$X){if($j["type"]=="enum")return(isset($_GET["select"])?"<label><input type='radio'$b value='orig' checked><i>".'original'."</i></label> ":"").enum_input("radio",$b,$j,$X,'empty');$C=$this->foreignKeyOptions($Q,$j["field"],$X);if($C!==null)return(is_array($C)?"<select$b>".optionlist($C,$X,true)."</select>":"<input value='".h($X)."'$b class='hidden'>"."<input value='".h($C)."' class='jsonly'>"."<div></div>".script("qsl('input').oninput = partial(whisper, '".ME."script=complete&source=".urlencode($Q)."&field=".urlencode($j["field"])."&value='); qsl('div').onclick = whisperClick;",""));if(like_bool($j))return'<input type="checkbox" value="1"'.(preg_match('~^(1|t|true|y|yes|on)$~i',$X)?' checked':'')."$b>";$zc="";if(preg_match('~time~',$j["type"]))$zc='HH:MM:SS';if(preg_match('~date|timestamp~',$j["type"]))$zc='[yyyy]-mm-dd'.($zc?" [$zc]":"");if($zc)return"<input value='".h($X)."'$b> ($zc)";if(preg_match('~_(md5|sha1)$~i',$j["field"]))return"<input type='password' value='".h($X)."'$b>";return'';}function
editHint($Q,$j,$X){return(preg_match('~\s+(\[.*\])$~',($j["comment"]!=""?$j["comment"]:$j["field"]),$z)?h(" $z[1]"):'');}function
processInput($j,$X,$o=""){if($o=="now")return"$o()";$J=$X;if(preg_match('~date|timestamp~',$j["type"])&&preg_match('(^'.str_replace('\$1','(?P<p1>\d*)',preg_replace('~(\\\\\\$([2-6]))~','(?P<p\2>\d{1,2})',preg_quote('$1-$3-$5'))).'(.*))',$X,$z))$J=($z["p1"]!=""?$z["p1"]:($z["p2"]!=""?($z["p2"]<70?20:19).$z["p2"]:gmdate("Y")))."-$z[p3]$z[p4]-$z[p5]$z[p6]".end($z);$J=q($J);if($X==""&&like_bool($j))$J="'0'";elseif($X==""&&($j["null"]||!preg_match('~char|text~',$j["type"])))$J="NULL";elseif(preg_match('~^(md5|sha1)$~',$o))$J="$o($J)";return
unconvert_field($j,$J);}function
dumpOutput(){return
array();}function
dumpFormat(){return
array('csv'=>'CSV,','csv;'=>'CSV;','tsv'=>'TSV');}function
dumpDatabase($g){}function
dumpTable($Q,$gf,$Wc=0){echo"\xef\xbb\xbf";}function
dumpData($Q,$gf,$H){$I=connection()->query($H,1);if($I){while($K=$I->fetch_assoc()){if($gf=="table"){dump_csv(array_keys($K));$gf="INSERT";}dump_csv($K);}}}function
dumpFilename($Ec){return
friendly_url($Ec);}function
dumpHeaders($Ec,$Cd=false){$Nb="csv";header("Content-Type: text/csv; charset=utf-8");return$Nb;}function
dumpFooter(){}function
importServerPath(){}function
homepage(){return
true;}function
navigation($_d){echo"<h1>".adminer()->name()." <span class='version'>".VERSION;$Gd=$_COOKIE["adminer_version"];echo" <a href='https://www.adminer.org/editor/#download'".target_blank()." id='version'>".(version_compare(VERSION,$Gd)<0?h($Gd):"")."</a>","</span></h1>\n";if($_d=="auth"){$Yb=true;foreach((array)$_SESSION["pwds"]as$Y=>$Re){foreach($Re[""]as$U=>$F){if($F!==null){if($Yb){echo"<ul id='logins'>",script("mixin(qs('#logins'), {onmouseover: menuOver, onmouseout: menuOut});");$Yb=false;}echo"<li><a href='".h(auth_url($Y,"",$U))."'>".($U!=""?h($U):"<i>".'empty'."</i>")."</a>\n";}}}}else{adminer()->databasesPrint($_d);if($_d!="db"&&$_d!="ns"){$R=table_status('',true);if(!$R)echo"<p class='message'>".'No tables.'."\n";else
adminer()->tablesPrint($R);}}}function
syntaxHighlighting($S){}function
databasesPrint($_d){}function
tablesPrint($S){echo"<ul id='tables'>",script("mixin(qs('#tables'), {onmouseover: menuOver, onmouseout: menuOut});");foreach($S
as$K){echo'<li>';$B=adminer()->tableName($K);if($B!="")echo"<a href='".h(ME).'select='.urlencode($K["Name"])."'".bold($_GET["select"]==$K["Name"]||$_GET["edit"]==$K["Name"],"select")." title='".'Select data'."'>$B</a>\n";}echo"</ul>\n";}function
_foreignColumn($ec,$c){foreach((array)$ec[$c]as$dc){if(count($dc["source"])==1){$B=adminer()->rowDescription($dc["table"]);if($B!=""){$r=idf_escape($dc["target"][0]);return
array($dc["table"],$r,$B);}}}}private
function
foreignKeyOptions($Q,$c,$X=null){if(list($nf,$r,$B)=$this->_foreignColumn(column_foreign_keys($Q),$c)){$J=&$this->values[$nf];if($J===null){$R=table_status1($nf);$J=($R["Rows"]>1000?"":array(""=>"")+get_key_vals("SELECT $r, $B FROM ".table($nf)." ORDER BY 2"));}if(!$J&&$X!==null)return
get_val("SELECT $B FROM ".table($nf)." WHERE $r = ".q($X));return$J;}}}class
Plugins{private
static$append=array('dumpFormat'=>true,'dumpOutput'=>true,'editRowPrint'=>true,'editFunctions'=>true,'config'=>true);var$plugins;var$error='';private$hooks=array();function
__construct($je){if($je===null){$je=array();$wa="adminer-plugins";if(is_dir($wa)){foreach(glob("$wa/*.php")as$l)$Ic=include_once"./$l";}$yc=" href='https://www.adminer.org/plugins/#use'".target_blank();if(file_exists("$wa.php")){$Ic=include_once"./$wa.php";if(is_array($Ic)){foreach($Ic
as$ie)$je[get_class($ie)]=$ie;}else$this->error
.=sprintf('%s must <a%s>return an array</a>.',"<b>$wa.php</b>",$yc)."<br>";}foreach(get_declared_classes()as$Ja){if(!$je[$Ja]&&preg_match('~^Adminer\w~i',$Ja)){$Ae=new
\ReflectionClass($Ja);$Ua=$Ae->getConstructor();if($Ua&&$Ua->getNumberOfRequiredParameters())$this->error
.=sprintf('<a%s>Configure</a> %s in %s.',$yc,"<b>$Ja</b>","<b>$wa.php</b>")."<br>";else$je[$Ja]=new$Ja;}}}$this->plugins=$je;$ba=new
Adminer;$je[]=$ba;$Ae=new
\ReflectionObject($ba);foreach($Ae->getMethods()as$zd){foreach($je
as$ie){$B=$zd->getName();if(method_exists($ie,$B))$this->hooks[$B][]=$ie;}}}function
__call($B,array$be){$ia=array();foreach($be
as$v=>$W)$ia[]=&$be[$v];$J=null;foreach($this->hooks[$B]as$ie){$X=call_user_func_array(array($ie,$B),$ia);if($X!==null){if(!self::$append[$B])return$X;$J=$X+(array)$J;}}return$J;}}abstract
class
Plugin{protected$translations=array();function
description(){return$this->lang('');}function
screenshot(){return"";}protected
function
lang($s,$Kd=null){$ia=func_get_args();$ia[0]=idx($this->translations[LANG],$s)?:$s;return
call_user_func_array('Adminer\lang_format',$ia);}}Adminer::$instance=(function_exists('adminer_object')?adminer_object():(is_dir("adminer-plugins")||file_exists("adminer-plugins.php")?new
Plugins(null):new
Adminer));SqlDriver::$drivers=array("server"=>"MySQL / MariaDB")+SqlDriver::$drivers;if(!defined('Adminer\DRIVER')){define('Adminer\DRIVER',"server");if(extension_loaded("mysqli")&&$_GET["ext"]!="pdo"){class
Db
extends
\MySQLi{static$instance;var$extension="MySQLi",$flavor='';function
__construct(){parent::init();}function
attach($N,$U,$F){mysqli_report(MYSQLI_REPORT_OFF);list($Ac,$ke)=host_port($N);$cf=adminer()->connectSsl();if($cf)$this->ssl_set($cf['key'],$cf['cert'],$cf['ca'],'','');$J=@$this->real_connect(($N!=""?$Ac:ini_get("mysqli.default_host")),($N.$U!=""?$U:ini_get("mysqli.default_user")),($N.$U.$F!=""?$F:ini_get("mysqli.default_pw")),null,(is_numeric($ke)?intval($ke):ini_get("mysqli.default_port")),(is_numeric($ke)?null:$ke),($cf?($cf['verify']!==false?2048:64):0));$this->options(MYSQLI_OPT_LOCAL_INFILE,0);return($J?'':$this->error);}function
set_charset($Ea){if(parent::set_charset($Ea))return
true;parent::set_charset('utf8');return$this->query("SET NAMES $Ea");}function
next_result(){return
self::more_results()&&parent::next_result();}function
quote($P){return"'".$this->escape_string($P)."'";}}}elseif(extension_loaded("mysql")&&!((ini_bool("sql.safe_mode")||ini_bool("mysql.allow_local_infile"))&&extension_loaded("pdo_mysql"))){class
Db
extends
SqlDb{private$link;function
attach($N,$U,$F){if(ini_bool("mysql.allow_local_infile"))return
sprintf('Disable %s or enable %s or %s extensions.',"'mysql.allow_local_infile'","MySQLi","PDO_MySQL");$this->link=@mysql_connect(($N!=""?$N:ini_get("mysql.default_host")),($N.$U!=""?$U:ini_get("mysql.default_user")),($N.$U.$F!=""?$F:ini_get("mysql.default_password")),true,131072);if(!$this->link)return
mysql_error();$this->server_info=mysql_get_server_info($this->link);return'';}function
set_charset($Ea){if(function_exists('mysql_set_charset')){if(mysql_set_charset($Ea,$this->link))return
true;mysql_set_charset('utf8',$this->link);}return$this->query("SET NAMES $Ea");}function
quote($P){return"'".mysql_real_escape_string($P,$this->link)."'";}function
select_db($gb){return
mysql_select_db($gb,$this->link);}function
query($H,$Ff=false){$I=@($Ff?mysql_unbuffered_query($H,$this->link):mysql_query($H,$this->link));$this->error="";if(!$I){$this->errno=mysql_errno($this->link);$this->error=mysql_error($this->link);return
false;}if($I===true){$this->affected_rows=mysql_affected_rows($this->link);$this->info=mysql_info($this->link);return
true;}return
new
Result($I);}}class
Result{var$num_rows;private$result;private$offset=0;function
__construct($I){$this->result=$I;$this->num_rows=mysql_num_rows($I);}function
fetch_assoc(){return
mysql_fetch_assoc($this->result);}function
fetch_row(){return
mysql_fetch_row($this->result);}function
fetch_field(){$J=mysql_fetch_field($this->result,$this->offset++);$J->orgtable=$J->table;$J->charsetnr=($J->blob?63:0);return$J;}function
__destruct(){mysql_free_result($this->result);}}}elseif(extension_loaded("pdo_mysql")){class
Db
extends
PdoDb{var$extension="PDO_MySQL";function
attach($N,$U,$F){$C=array(\PDO::MYSQL_ATTR_LOCAL_INFILE=>false);$cf=adminer()->connectSsl();if($cf){if($cf['key'])$C[\PDO::MYSQL_ATTR_SSL_KEY]=$cf['key'];if($cf['cert'])$C[\PDO::MYSQL_ATTR_SSL_CERT]=$cf['cert'];if($cf['ca'])$C[\PDO::MYSQL_ATTR_SSL_CA]=$cf['ca'];if(isset($cf['verify']))$C[\PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT]=$cf['verify'];}list($Ac,$ke)=host_port($N);return$this->dsn("mysql:charset=utf8;host=$Ac".($ke?(is_numeric($ke)?";port=":";unix_socket=").$ke:""),$U,$F,$C);}function
set_charset($Ea){return$this->query("SET NAMES $Ea");}function
select_db($gb){return$this->query("USE ".idf_escape($gb));}function
query($H,$Ff=false){$this->pdo->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY,!$Ff);return
parent::query($H,$Ff);}}}class
Driver
extends
SqlDriver{static$extensions=array("MySQLi","MySQL","PDO_MySQL");static$jush="sql";var$unsigned=array("unsigned","zerofill","unsigned zerofill");var$operators=array("=","<",">","<=",">=","!=","LIKE","LIKE %%","REGEXP","IN","FIND_IN_SET","IS NULL","NOT LIKE","NOT REGEXP","NOT IN","IS NOT NULL","SQL");var$functions=array("char_length","date","from_unixtime","lower","round","floor","ceil","sec_to_time","time_to_sec","upper");var$grouping=array("avg","count","count distinct","group_concat","max","min","sum");static
function
connect($N,$U,$F){$e=parent::connect($N,$U,$F);if(is_string($e)){if(function_exists('iconv')&&!is_utf8($e)&&strlen($He=iconv("windows-1250","utf-8",$e))>strlen($e))$e=$He;return$e;}$e->set_charset(charset($e));$e->query("SET sql_quote_show_create = 1, autocommit = 1");$e->flavor=(preg_match('~MariaDB~',$e->server_info)?'maria':'mysql');add_driver(DRIVER,($e->flavor=='maria'?"MariaDB":"MySQL"));return$e;}function
__construct(Db$e){parent::__construct($e);$this->types=array('Numbers'=>array("tinyint"=>3,"smallint"=>5,"mediumint"=>8,"int"=>10,"bigint"=>20,"decimal"=>66,"float"=>12,"double"=>21),'Date and time'=>array("date"=>10,"datetime"=>19,"timestamp"=>19,"time"=>10,"year"=>4),'Strings'=>array("char"=>255,"varchar"=>65535,"tinytext"=>255,"text"=>65535,"mediumtext"=>16777215,"longtext"=>4294967295),'Lists'=>array("enum"=>65535,"set"=>64),'Binary'=>array("bit"=>20,"binary"=>255,"varbinary"=>65535,"tinyblob"=>255,"blob"=>65535,"mediumblob"=>16777215,"longblob"=>4294967295),'Geometry'=>array("geometry"=>0,"point"=>0,"linestring"=>0,"polygon"=>0,"multipoint"=>0,"multilinestring"=>0,"multipolygon"=>0,"geometrycollection"=>0),);$this->insertFunctions=array("char"=>"md5/sha1/password/encrypt/uuid","binary"=>"md5/sha1","date|time"=>"now",);$this->editFunctions=array(number_type()=>"+/-","date"=>"+ interval/- interval","time"=>"addtime/subtime","char|text"=>"concat",);if(min_version('5.7.8',10.2,$e))$this->types['Strings']["json"]=4294967295;if(min_version('',10.7,$e)){$this->types['Strings']["uuid"]=128;$this->insertFunctions['uuid']='uuid';}if(min_version(9,'',$e)){$this->types['Numbers']["vector"]=16383;$this->insertFunctions['vector']='string_to_vector';}if(min_version(5.1,'',$e))$this->partitionBy=array("HASH","LINEAR HASH","KEY","LINEAR KEY","RANGE","LIST");if(min_version(5.7,10.2,$e))$this->generated=array("STORED","VIRTUAL");}function
unconvertFunction(array$j){return(preg_match("~binary~",$j["type"])?"<code class='jush-sql'>UNHEX</code>":($j["type"]=="bit"?doc_link(array('sql'=>'bit-value-literals.html'),"<code>b''</code>"):(preg_match("~geometry|point|linestring|polygon~",$j["type"])?"<code class='jush-sql'>GeomFromText</code>":"")));}function
insert($Q,array$O){return($O?parent::insert($Q,$O):queries("INSERT INTO ".table($Q)." ()\nVALUES ()"));}function
insertUpdate($Q,array$L,array$pe){$d=array_keys(reset($L));$oe="INSERT INTO ".table($Q)." (".implode(", ",$d).") VALUES\n";$Uf=array();foreach($d
as$v)$Uf[$v]="$v = VALUES($v)";$if="\nON DUPLICATE KEY UPDATE ".implode(", ",$Uf);$Uf=array();$hd=0;foreach($L
as$O){$X="(".implode(", ",$O).")";if($Uf&&(strlen($oe)+$hd+strlen($X)+strlen($if)>1e6)){if(!queries($oe.implode(",\n",$Uf).$if))return
false;$Uf=array();$hd=0;}$Uf[]=$X;$hd+=strlen($X)+2;}return
queries($oe.implode(",\n",$Uf).$if);}function
slowQuery($H,$sf){if(min_version('5.7.8','10.1.2')){if($this->conn->flavor=='maria')return"SET STATEMENT max_statement_time=$sf FOR $H";elseif(preg_match('~^(SELECT\b)(.+)~is',$H,$z))return"$z[1] /*+ MAX_EXECUTION_TIME(".($sf*1000).") */ $z[2]";}}function
convertSearch($s,array$W,array$j){return(preg_match('~char|text|enum|set~',$j["type"])&&!preg_match("~^utf8~",$j["collation"])&&preg_match('~[\x80-\xFF]~',$W['val'])?"CONVERT($s USING ".charset($this->conn).")":$s);}function
warnings(){$I=$this->conn->query("SHOW WARNINGS");if($I&&$I->num_rows){ob_start();print_select_result($I);return
ob_get_clean();}}function
tableHelp($B,$Wc=false){$md=($this->conn->flavor=='maria');if(information_schema(DB))return
strtolower("information-schema-".($md?"$B-table/":str_replace("_","-",$B)."-table.html"));if(DB=="mysql")return($md?"mysql$B-table/":"system-schema.html");}function
partitionsInfo($Q){$ic="FROM information_schema.PARTITIONS WHERE TABLE_SCHEMA = ".q(DB)." AND TABLE_NAME = ".q($Q);$I=$this->conn->query("SELECT PARTITION_METHOD, PARTITION_EXPRESSION, PARTITION_ORDINAL_POSITION $ic ORDER BY PARTITION_ORDINAL_POSITION DESC LIMIT 1");$J=array();list($J["partition_by"],$J["partition"],$J["partitions"])=$I->fetch_row();$ee=get_key_vals("SELECT PARTITION_NAME, PARTITION_DESCRIPTION $ic AND PARTITION_NAME != '' ORDER BY PARTITION_ORDINAL_POSITION");$J["partition_names"]=array_keys($ee);$J["partition_values"]=array_values($ee);return$J;}function
hasCStyleEscapes(){static$Ca;if($Ca===null){$bf=get_val("SHOW VARIABLES LIKE 'sql_mode'",1,$this->conn);$Ca=(strpos($bf,'NO_BACKSLASH_ESCAPES')===false);}return$Ca;}function
engines(){$J=array();foreach(get_rows("SHOW ENGINES")as$K){if(preg_match("~YES|DEFAULT~",$K["Support"]))$J[]=$K["Engine"];}return$J;}function
indexAlgorithms(array$lf){return(preg_match('~^(MEMORY|NDB)$~',$lf["Engine"])?array("HASH","BTREE"):array());}}function
idf_escape($s){return"`".str_replace("`","``",$s)."`";}function
table($s){return
idf_escape($s);}function
get_databases($ac){$J=get_session("dbs");if($J===null){$H="SELECT SCHEMA_NAME FROM information_schema.SCHEMATA ORDER BY SCHEMA_NAME";$J=($ac?slow_query($H):get_vals($H));restart_session();set_session("dbs",$J);stop_session();}return$J;}function
limit($H,$Z,$w,$Ld=0,$Pe=" "){return" $H$Z".($w?$Pe."LIMIT $w".($Ld?" OFFSET $Ld":""):"");}function
limit1($Q,$H,$Z,$Pe="\n"){return
limit($H,$Z,1,0,$Pe);}function
db_collation($g,array$Oa){$J=null;$Ya=get_val("SHOW CREATE DATABASE ".idf_escape($g),1);if(preg_match('~ COLLATE ([^ ]+)~',$Ya,$z))$J=$z[1];elseif(preg_match('~ CHARACTER SET ([^ ]+)~',$Ya,$z))$J=$Oa[$z[1]][-1];return$J;}function
logged_user(){return
get_val("SELECT USER()");}function
tables_list(){return
get_key_vals("SELECT TABLE_NAME, TABLE_TYPE FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() ORDER BY TABLE_NAME");}function
count_tables(array$hb){$J=array();foreach($hb
as$g)$J[$g]=count(get_vals("SHOW TABLES IN ".idf_escape($g)));return$J;}function
table_status($B="",$Sb=false){$J=array();foreach(get_rows($Sb?"SELECT TABLE_NAME AS Name, ENGINE AS Engine, TABLE_COMMENT AS Comment FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() ".($B!=""?"AND TABLE_NAME = ".q($B):"ORDER BY Name"):"SHOW TABLE STATUS".($B!=""?" LIKE ".q(addcslashes($B,"%_\\")):""))as$K){if($K["Engine"]=="InnoDB")$K["Comment"]=preg_replace('~(?:(.+); )?InnoDB free: .*~','\1',$K["Comment"]);if(!isset($K["Engine"]))$K["Comment"]="";if($B!="")$K["Name"]=$B;$J[$K["Name"]]=$K;}return$J;}function
is_view(array$R){return$R["Engine"]===null;}function
fk_support(array$R){return
preg_match('~InnoDB|IBMDB2I'.(min_version(5.6)?'|NDB':'').'~i',$R["Engine"]);}function
fields($Q){$md=(connection()->flavor=='maria');$J=array();foreach(get_rows("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ".q($Q)." ORDER BY ORDINAL_POSITION")as$K){$j=$K["COLUMN_NAME"];$T=$K["COLUMN_TYPE"];$mc=$K["GENERATION_EXPRESSION"];$Qb=$K["EXTRA"];preg_match('~^(VIRTUAL|PERSISTENT|STORED)~',$Qb,$lc);preg_match('~^([^( ]+)(?:\((.+)\))?( unsigned)?( zerofill)?$~',$T,$od);$h=$K["COLUMN_DEFAULT"];if($h!=""){$Vc=preg_match('~text|json~',$od[1]);if(!$md&&$Vc)$h=preg_replace("~^(_\w+)?('.*')$~",'\2',stripslashes($h));if($md||$Vc){$h=($h=="NULL"?null:preg_replace_callback("~^'(.*)'$~",function($z){return
stripslashes(str_replace("''","'",$z[1]));},$h));}if(!$md&&preg_match('~binary~',$od[1])&&preg_match('~^0x(\w*)$~',$h,$z))$h=pack("H*",$z[1]);}$J[$j]=array("field"=>$j,"full_type"=>$T,"type"=>$od[1],"length"=>$od[2],"unsigned"=>ltrim($od[3].$od[4]),"default"=>($lc?($md?$mc:stripslashes($mc)):$h),"null"=>($K["IS_NULLABLE"]=="YES"),"auto_increment"=>($Qb=="auto_increment"),"on_update"=>(preg_match('~\bon update (\w+)~i',$Qb,$z)?$z[1]:""),"collation"=>$K["COLLATION_NAME"],"privileges"=>array_flip(explode(",","$K[PRIVILEGES],where,order")),"comment"=>$K["COLUMN_COMMENT"],"primary"=>($K["COLUMN_KEY"]=="PRI"),"generated"=>($lc[1]=="PERSISTENT"?"STORED":$lc[1]),);}return$J;}function
indexes($Q,$f=null){$J=array();foreach(get_rows("SHOW INDEX FROM ".table($Q),$f)as$K){$B=$K["Key_name"];$J[$B]["type"]=($B=="PRIMARY"?"PRIMARY":($K["Index_type"]=="FULLTEXT"?"FULLTEXT":($K["Non_unique"]?($K["Index_type"]=="SPATIAL"?"SPATIAL":"INDEX"):"UNIQUE")));$J[$B]["columns"][]=$K["Column_name"];$J[$B]["lengths"][]=($K["Index_type"]=="SPATIAL"?null:$K["Sub_part"]);$J[$B]["descs"][]=null;$J[$B]["algorithm"]=$K["Index_type"];}return$J;}function
foreign_keys($Q){static$ge='(?:`(?:[^`]|``)+`|"(?:[^"]|"")+")';$J=array();$Za=get_val("SHOW CREATE TABLE ".table($Q),1);if($Za){preg_match_all("~CONSTRAINT ($ge) FOREIGN KEY ?\\(((?:$ge,? ?)+)\\) REFERENCES ($ge)(?:\\.($ge))? \\(((?:$ge,? ?)+)\\)(?: ON DELETE (".driver()->onActions."))?(?: ON UPDATE (".driver()->onActions."))?~",$Za,$pd,PREG_SET_ORDER);foreach($pd
as$z){preg_match_all("~$ge~",$z[2],$Ye);preg_match_all("~$ge~",$z[5],$nf);$J[idf_unescape($z[1])]=array("db"=>idf_unescape($z[4]!=""?$z[3]:$z[4]),"table"=>idf_unescape($z[4]!=""?$z[4]:$z[3]),"source"=>array_map('Adminer\idf_unescape',$Ye[0]),"target"=>array_map('Adminer\idf_unescape',$nf[0]),"on_delete"=>($z[6]?:"RESTRICT"),"on_update"=>($z[7]?:"RESTRICT"),);}}return$J;}function
view($B){return
array("select"=>preg_replace('~^(?:[^`]|`[^`]*`)*\s+AS\s+~isU','',get_val("SHOW CREATE VIEW ".table($B),1)));}function
collations(){$J=array();foreach(get_rows("SHOW COLLATION")as$K){if($K["Default"])$J[$K["Charset"]][-1]=$K["Collation"];else$J[$K["Charset"]][]=$K["Collation"];}ksort($J);foreach($J
as$v=>$W)sort($J[$v]);return$J;}function
information_schema($g){return($g=="information_schema")||(min_version(5.5)&&$g=="performance_schema");}function
error(){return
h(preg_replace('~^You have an error.*syntax to use~U',"Syntax error",connection()->error));}function
create_database($g,$Na){return
queries("CREATE DATABASE ".idf_escape($g).($Na?" COLLATE ".q($Na):""));}function
drop_databases(array$hb){$J=apply_queries("DROP DATABASE",$hb,'Adminer\idf_escape');restart_session();set_session("dbs",null);return$J;}function
rename_database($B,$Na){$J=false;if(create_database($B,$Na)){$S=array();$Xf=array();foreach(tables_list()as$Q=>$T){if($T=='VIEW')$Xf[]=$Q;else$S[]=$Q;}$J=(!$S&&!$Xf)||move_tables($S,$Xf,$B);drop_databases($J?array(DB):array());}return$J;}function
auto_increment(){$pa=" PRIMARY KEY";if($_GET["create"]!=""&&$_POST["auto_increment_col"]){foreach(indexes($_GET["create"])as$t){if(in_array($_POST["fields"][$_POST["auto_increment_col"]]["orig"],$t["columns"],true)){$pa="";break;}if($t["type"]=="PRIMARY")$pa=" UNIQUE";}}return" AUTO_INCREMENT$pa";}function
alter_table($Q,$B,array$k,array$cc,$Ra,$Cb,$Na,$oa,$de){$ga=array();foreach($k
as$j){if($j[1]){$h=$j[1][3];if(preg_match('~ GENERATED~',$h)){$j[1][3]=(connection()->flavor=='maria'?"":$j[1][2]);$j[1][2]=$h;}$ga[]=($Q!=""?($j[0]!=""?"CHANGE ".idf_escape($j[0]):"ADD"):" ")." ".implode($j[1]).($Q!=""?$j[2]:"");}else$ga[]="DROP ".idf_escape($j[0]);}$ga=array_merge($ga,$cc);$ef=($Ra!==null?" COMMENT=".q($Ra):"").($Cb?" ENGINE=".q($Cb):"").($Na?" COLLATE ".q($Na):"").($oa!=""?" AUTO_INCREMENT=$oa":"");if($de){$ee=array();if($de["partition_by"]=='RANGE'||$de["partition_by"]=='LIST'){foreach($de["partition_names"]as$v=>$W){$X=$de["partition_values"][$v];$ee[]="\n  PARTITION ".idf_escape($W)." VALUES ".($de["partition_by"]=='RANGE'?"LESS THAN":"IN").($X!=""?" ($X)":" MAXVALUE");}}$ef
.="\nPARTITION BY $de[partition_by]($de[partition])";if($ee)$ef
.=" (".implode(",",$ee)."\n)";elseif($de["partitions"])$ef
.=" PARTITIONS ".(+$de["partitions"]);}elseif($de===null)$ef
.="\nREMOVE PARTITIONING";if($Q=="")return
queries("CREATE TABLE ".table($B)." (\n".implode(",\n",$ga)."\n)$ef");if($Q!=$B)$ga[]="RENAME TO ".table($B);if($ef)$ga[]=ltrim($ef);return($ga?queries("ALTER TABLE ".table($Q)."\n".implode(",\n",$ga)):true);}function
alter_indexes($Q,$ga){$Da=array();foreach($ga
as$W)$Da[]=($W[2]=="DROP"?"\nDROP INDEX ".idf_escape($W[1]):"\nADD $W[0] ".($W[0]=="PRIMARY"?"KEY ":"").($W[1]!=""?idf_escape($W[1])." ":"")."(".implode(", ",$W[2]).")");return
queries("ALTER TABLE ".table($Q).implode(",",$Da));}function
truncate_tables(array$S){return
apply_queries("TRUNCATE TABLE",$S);}function
drop_views(array$Xf){return
queries("DROP VIEW ".implode(", ",array_map('Adminer\table',$Xf)));}function
drop_tables(array$S){return
queries("DROP TABLE ".implode(", ",array_map('Adminer\table',$S)));}function
move_tables(array$S,array$Xf,$nf){$Ce=array();foreach($S
as$Q)$Ce[]=table($Q)." TO ".idf_escape($nf).".".table($Q);if(!$Ce||queries("RENAME TABLE ".implode(", ",$Ce))){$kb=array();foreach($Xf
as$Q)$kb[table($Q)]=view($Q);connection()->select_db($nf);$g=idf_escape(DB);foreach($kb
as$B=>$Wf){if(!queries("CREATE VIEW $B AS ".str_replace(" $g."," ",$Wf["select"]))||!queries("DROP VIEW $g.$B"))return
false;}return
true;}return
false;}function
copy_tables(array$S,array$Xf,$nf){queries("SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO'");foreach($S
as$Q){$B=($nf==DB?table("copy_$Q"):idf_escape($nf).".".table($Q));if(($_POST["overwrite"]&&!queries("\nDROP TABLE IF EXISTS $B"))||!queries("CREATE TABLE $B LIKE ".table($Q))||!queries("INSERT INTO $B SELECT * FROM ".table($Q)))return
false;foreach(get_rows("SHOW TRIGGERS LIKE ".q(addcslashes($Q,"%_\\")))as$K){$Cf=$K["Trigger"];if(!queries("CREATE TRIGGER ".($nf==DB?idf_escape("copy_$Cf"):idf_escape($nf).".".idf_escape($Cf))." $K[Timing] $K[Event] ON $B FOR EACH ROW\n$K[Statement];"))return
false;}}foreach($Xf
as$Q){$B=($nf==DB?table("copy_$Q"):idf_escape($nf).".".table($Q));$Wf=view($Q);if(($_POST["overwrite"]&&!queries("DROP VIEW IF EXISTS $B"))||!queries("CREATE VIEW $B AS $Wf[select]"))return
false;}return
true;}function
trigger($B,$Q){if($B=="")return
array();$L=get_rows("SHOW TRIGGERS WHERE `Trigger` = ".q($B));return
reset($L);}function
triggers($Q){$J=array();foreach(get_rows("SHOW TRIGGERS LIKE ".q(addcslashes($Q,"%_\\")))as$K)$J[$K["Trigger"]]=array($K["Timing"],$K["Event"]);return$J;}function
trigger_options(){return
array("Timing"=>array("BEFORE","AFTER"),"Event"=>array("INSERT","UPDATE","DELETE"),"Type"=>array("FOR EACH ROW"),);}function
routine($B,$T){$fa=array("bool","boolean","integer","double precision","real","dec","numeric","fixed","national char","national varchar");$Ze="(?:\\s|/\\*[\s\S]*?\\*/|(?:#|-- )[^\n]*\n?|--\r?\n)";$Db=driver()->enumLength;$Df="((".implode("|",array_merge(array_keys(driver()->types()),$fa)).")\\b(?:\\s*\\(((?:[^'\")]|$Db)++)\\))?"."\\s*(zerofill\\s*)?(unsigned(?:\\s+zerofill)?)?)(?:\\s*(?:CHARSET|CHARACTER\\s+SET)\\s*['\"]?([^'\"\\s,]+)['\"]?)?(?:\\s*COLLATE\\s*['\"]?[^'\"\\s,]+['\"]?)?";$ge="$Ze*(".($T=="FUNCTION"?"":driver()->inout).")?\\s*(?:`((?:[^`]|``)*)`\\s*|\\b(\\S+)\\s+)$Df";$Ya=get_val("SHOW CREATE $T ".idf_escape($B),2);preg_match("~\\(((?:$ge\\s*,?)*)\\)\\s*".($T=="FUNCTION"?"RETURNS\\s+$Df\\s+":"")."(.*)~is",$Ya,$z);$k=array();preg_match_all("~$ge\\s*,?~is",$z[1],$pd,PREG_SET_ORDER);foreach($pd
as$ae)$k[]=array("field"=>str_replace("``","`",$ae[2]).$ae[3],"type"=>strtolower($ae[5]),"length"=>preg_replace_callback("~$Db~s",'Adminer\normalize_enum',$ae[6]),"unsigned"=>strtolower(preg_replace('~\s+~',' ',trim("$ae[8] $ae[7]"))),"null"=>true,"full_type"=>$ae[4],"inout"=>strtoupper($ae[1]),"collation"=>strtolower($ae[9]),);return
array("fields"=>$k,"comment"=>get_val("SELECT ROUTINE_COMMENT FROM information_schema.ROUTINES WHERE ROUTINE_SCHEMA = DATABASE() AND ROUTINE_NAME = ".q($B)),)+($T!="FUNCTION"?array("definition"=>$z[11]):array("returns"=>array("type"=>$z[12],"length"=>$z[13],"unsigned"=>$z[15],"collation"=>$z[16]),"definition"=>$z[17],"language"=>"SQL",));}function
routines(){return
get_rows("SELECT SPECIFIC_NAME, ROUTINE_NAME, ROUTINE_TYPE, DTD_IDENTIFIER FROM information_schema.ROUTINES WHERE ROUTINE_SCHEMA = DATABASE()");}function
routine_languages(){return
array();}function
routine_id($B,array$K){return
idf_escape($B);}function
last_id($I){return
get_val("SELECT LAST_INSERT_ID()");}function
explain(Db$e,$H){return$e->query("EXPLAIN ".(min_version(5.1)&&!min_version(5.7)?"PARTITIONS ":"").$H);}function
found_rows(array$R,array$Z){return($Z||$R["Engine"]!="InnoDB"?null:$R["Rows"]);}function
create_sql($Q,$oa,$gf){$J=get_val("SHOW CREATE TABLE ".table($Q),1);if(!$oa)$J=preg_replace('~ AUTO_INCREMENT=\d+~','',$J);return$J;}function
truncate_sql($Q){return"TRUNCATE ".table($Q);}function
use_sql($gb,$gf=""){$B=idf_escape($gb);$J="";if(preg_match('~CREATE~',$gf)&&($Ya=get_val("SHOW CREATE DATABASE $B",1))){set_utf8mb4($Ya);if($gf=="DROP+CREATE")$J="DROP DATABASE IF EXISTS $B;\n";$J
.="$Ya;\n";}return$J."USE $B";}function
trigger_sql($Q){$J="";foreach(get_rows("SHOW TRIGGERS LIKE ".q(addcslashes($Q,"%_\\")),null,"-- ")as$K)$J
.="\nCREATE TRIGGER ".idf_escape($K["Trigger"])." $K[Timing] $K[Event] ON ".table($K["Table"])." FOR EACH ROW\n$K[Statement];;\n";return$J;}function
show_variables(){return
get_rows("SHOW VARIABLES");}function
show_status(){return
get_rows("SHOW STATUS");}function
process_list(){return
get_rows("SHOW FULL PROCESSLIST");}function
convert_field(array$j){if(preg_match("~binary~",$j["type"]))return"HEX(".idf_escape($j["field"]).")";if($j["type"]=="bit")return"BIN(".idf_escape($j["field"])." + 0)";if(preg_match("~geometry|point|linestring|polygon~",$j["type"]))return(min_version(8)?"ST_":"")."AsWKT(".idf_escape($j["field"]).")";}function
unconvert_field(array$j,$J){if(preg_match("~binary~",$j["type"]))$J="UNHEX($J)";if($j["type"]=="bit")$J="CONVERT(b$J, UNSIGNED)";if(preg_match("~geometry|point|linestring|polygon~",$j["type"])){$oe=(min_version(8)?"ST_":"");$J=$oe."GeomFromText($J, $oe"."SRID($j[field]))";}return$J;}function
support($Tb){return
preg_match('~^(comment|columns|copy|database|drop_col|dump|indexes|kill|privileges|move_col|procedure|processlist|routine|sql|status|table|trigger|variables|view'.(min_version(5.1)?'|event':'').(min_version(8)?'|descidx':'').(min_version('8.0.16','10.2.1')?'|check':'').')$~',$Tb);}function
kill_process($r){return
queries("KILL ".number($r));}function
connection_id(){return"SELECT CONNECTION_ID()";}function
max_connections(){return
get_val("SELECT @@max_connections");}function
types(){return
array();}function
type_values($r){return"";}function
schemas(){return
array();}function
get_schema(){return"";}function
set_schema($Ie,$f=null){return
true;}}define('Adminer\JUSH',Driver::$jush);define('Adminer\SERVER',"".$_GET[DRIVER]);define('Adminer\DB',"$_GET[db]");define('Adminer\ME',preg_replace('~\?.*~','',relative_uri()).'?'.(sid()?SID.'&':'').(SERVER!==null?DRIVER."=".urlencode(SERVER).'&':'').($_GET["ext"]?"ext=".urlencode($_GET["ext"]).'&':'').(isset($_GET["username"])?"username=".urlencode($_GET["username"]).'&':'').(DB!=""?'db='.urlencode(DB).'&'.(isset($_GET["ns"])?"ns=".urlencode($_GET["ns"])."&":""):''));function
page_header($tf,$i="",$Ba=array(),$uf=""){page_headers();if(is_ajax()&&$i){page_messages($i);exit;}if(!ob_get_level())ob_start('ob_gzhandler',4096);$vf=$tf.($uf!=""?": $uf":"");$wf=strip_tags($vf.(SERVER!=""&&SERVER!="localhost"?h(" - ".SERVER):"")." - ".adminer()->name());echo'<!DOCTYPE html>
<html lang="en" dir="ltr">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="robots" content="noindex">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>',$wf,'</title>
<link rel="stylesheet" href="',h(preg_replace("~\\?.*~","",ME)."?file=default.css&version=5.4.1"),'">
';$cb=adminer()->css();if(is_int(key($cb)))$cb=array_fill_keys($cb,'light');$uc=in_array('light',$cb)||in_array('',$cb);$rc=in_array('dark',$cb)||in_array('',$cb);$eb=($uc?($rc?null:false):($rc?:null));$xd=" media='(prefers-color-scheme: dark)'";if($eb!==false)echo"<link rel='stylesheet'".($eb?"":$xd)." href='".h(preg_replace("~\\?.*~","",ME)."?file=dark.css&version=5.4.1")."'>\n";echo"<meta name='color-scheme' content='".($eb===null?"light dark":($eb?"dark":"light"))."'>\n",script_src(preg_replace("~\\?.*~","",ME)."?file=functions.js&version=5.4.1");if(adminer()->head($eb))echo"<link rel='icon' href='data:image/gif;base64,R0lGODlhEAAQAJEAAAQCBPz+/PwCBAROZCH5BAEAAAAALAAAAAAQABAAAAI2hI+pGO1rmghihiUdvUBnZ3XBQA7f05mOak1RWXrNq5nQWHMKvuoJ37BhVEEfYxQzHjWQ5qIAADs='>\n","<link rel='apple-touch-icon' href='".h(preg_replace("~\\?.*~","",ME)."?file=logo.png&version=5.4.1")."'>\n";foreach($cb
as$Qf=>$Ad){$b=($Ad=='dark'&&!$eb?$xd:($Ad=='light'&&$rc?" media='(prefers-color-scheme: light)'":""));echo"<link rel='stylesheet'$b href='".h($Qf)."'>\n";}echo"\n<body class='".'ltr'." nojs";adminer()->bodyClass();echo"'>\n";$l=get_temp_dir()."/adminer.version";if(!$_COOKIE["adminer_version"]&&function_exists('openssl_verify')&&file_exists($l)&&filemtime($l)+86400>time()){$Vf=unserialize(file_get_contents($l));$te="-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAwqWOVuF5uw7/+Z70djoK
RlHIZFZPO0uYRezq90+7Amk+FDNd7KkL5eDve+vHRJBLAszF/7XKXe11xwliIsFs
DFWQlsABVZB3oisKCBEuI71J4kPH8dKGEWR9jDHFw3cWmoH3PmqImX6FISWbG3B8
h7FIx3jEaw5ckVPVTeo5JRm/1DZzJxjyDenXvBQ/6o9DgZKeNDgxwKzH+sw9/YCO
jHnq1cFpOIISzARlrHMa/43YfeNRAm/tsBXjSxembBPo7aQZLAWHmaj5+K19H10B
nCpz9Y++cipkVEiKRGih4ZEvjoFysEOdRLj6WiD/uUNky4xGeA6LaJqh5XpkFkcQ
fQIDAQAB
-----END PUBLIC KEY-----
";if(openssl_verify($Vf["version"],base64_decode($Vf["signature"]),$te)==1)$_COOKIE["adminer_version"]=$Vf["version"];}echo
script("mixin(document.body, {onkeydown: bodyKeydown, onclick: bodyClick".(isset($_COOKIE["adminer_version"])?"":", onload: partial(verifyVersion, '".VERSION."', '".js_escape(ME)."', '".get_token()."')")."});
document.body.classList.replace('nojs', 'js');
const offlineMessage = '".js_escape('You are offline.')."';
const thousandsSeparator = '".js_escape(',')."';"),"<div id='help' class='jush-".JUSH." jsonly hidden'></div>\n",script("mixin(qs('#help'), {onmouseover: () => { helpOpen = 1; }, onmouseout: helpMouseout});"),"<div id='content'>\n","<span id='menuopen' class='jsonly'>".icon("move","","menu","")."</span>".script("qs('#menuopen').onclick = event => { qs('#foot').classList.toggle('foot'); event.stopPropagation(); }");if($Ba!==null){$x=substr(preg_replace('~\b(username|db|ns)=[^&]*&~','',ME),0,-1);echo'<p id="breadcrumb"><a href="'.h($x?:".").'">'.get_driver(DRIVER).'</a> ¬ª ';$x=substr(preg_replace('~\b(db|ns)=[^&]*&~','',ME),0,-1);$N=adminer()->serverName(SERVER);$N=($N!=""?$N:'Server');if($Ba===false)echo"$N\n";else{echo"<a href='".h($x)."' accesskey='1' title='Alt+Shift+1'>$N</a> ¬ª ";if($_GET["ns"]!=""||(DB!=""&&is_array($Ba)))echo'<a href="'.h($x."&db=".urlencode(DB).(support("scheme")?"&ns=":"")).'">'.h(DB).'</a> ¬ª ';if(is_array($Ba)){if($_GET["ns"]!="")echo'<a href="'.h(substr(ME,0,-1)).'">'.h($_GET["ns"]).'</a> ¬ª ';foreach($Ba
as$v=>$W){$mb=(is_array($W)?$W[1]:h($W));if($mb!="")echo"<a href='".h(ME."$v=").urlencode(is_array($W)?$W[0]:$W)."'>$mb</a> ¬ª ";}}echo"$tf\n";}}echo"<h2>$vf</h2>\n","<div id='ajaxstatus' class='jsonly hidden'></div>\n";restart_session();page_messages($i);$hb=&get_session("dbs");if(DB!=""&&$hb&&!in_array(DB,$hb,true))$hb=null;stop_session();define('Adminer\PAGE_HEADER',1);}function
page_headers(){header("Content-Type: text/html; charset=utf-8");header("Cache-Control: no-cache");header("X-Frame-Options: deny");header("X-XSS-Protection: 0");header("X-Content-Type-Options: nosniff");header("Referrer-Policy: origin-when-cross-origin");foreach(adminer()->csp(csp())as$bb){$vc=array();foreach($bb
as$v=>$W)$vc[]="$v $W";header("Content-Security-Policy: ".implode("; ",$vc));}adminer()->headers();}function
csp(){return
array(array("script-src"=>"'self' 'unsafe-inline' 'nonce-".get_nonce()."' 'strict-dynamic'","connect-src"=>"'self'","frame-src"=>"https://www.adminer.org","object-src"=>"'none'","base-uri"=>"'none'","form-action"=>"'self'",),);}function
get_nonce(){static$Id;if(!$Id)$Id=base64_encode(rand_string());return$Id;}function
page_messages($i){$Pf=preg_replace('~^[^?]*~','',$_SERVER["REQUEST_URI"]);$yd=idx($_SESSION["messages"],$Pf);if($yd){echo"<div class='message'>".implode("</div>\n<div class='message'>",$yd)."</div>".script("messagesPrint();");unset($_SESSION["messages"][$Pf]);}if($i)echo"<div class='error'>$i</div>\n";if(adminer()->error)echo"<div class='error'>".adminer()->error."</div>\n";}function
page_footer($_d=""){echo"</div>\n\n<div id='foot' class='foot'>\n<div id='menu'>\n";adminer()->navigation($_d);echo"</div>\n";if($_d!="auth")echo'<form action="" method="post">
<p class="logout">
<span>',h($_GET["username"])."\n",'</span>
<input type="submit" name="logout" value="Logout" id="logout">
',input_token(),'</form>
';echo"</div>\n\n",script("setupSubmitHighlight(document);");}function
int32($A){while($A>=2147483648)$A-=4294967296;while($A<=-2147483649)$A+=4294967296;return(int)$A;}function
long2str(array$V,$Zf){$He='';foreach($V
as$W)$He
.=pack('V',$W);if($Zf)return
substr($He,0,end($V));return$He;}function
str2long($He,$Zf){$V=array_values(unpack('V*',str_pad($He,4*ceil(strlen($He)/4),"\0")));if($Zf)$V[]=strlen($He);return$V;}function
xxtea_mx($eg,$dg,$jf,$Yc){return
int32((($eg>>5&0x7FFFFFF)^$dg<<2)+(($dg>>3&0x1FFFFFFF)^$eg<<4))^int32(($jf^$dg)+($Yc^$eg));}function
encrypt_string($ff,$v){if($ff=="")return"";$v=array_values(unpack("V*",pack("H*",md5($v))));$V=str2long($ff,true);$A=count($V)-1;$eg=$V[$A];$dg=$V[0];$ue=floor(6+52/($A+1));$jf=0;while($ue-->0){$jf=int32($jf+0x9E3779B9);$vb=$jf>>2&3;for($Yd=0;$Yd<$A;$Yd++){$dg=$V[$Yd+1];$Dd=xxtea_mx($eg,$dg,$jf,$v[$Yd&3^$vb]);$eg=int32($V[$Yd]+$Dd);$V[$Yd]=$eg;}$dg=$V[0];$Dd=xxtea_mx($eg,$dg,$jf,$v[$Yd&3^$vb]);$eg=int32($V[$A]+$Dd);$V[$A]=$eg;}return
long2str($V,false);}function
decrypt_string($ff,$v){if($ff=="")return"";if(!$v)return
false;$v=array_values(unpack("V*",pack("H*",md5($v))));$V=str2long($ff,false);$A=count($V)-1;$eg=$V[$A];$dg=$V[0];$ue=floor(6+52/($A+1));$jf=int32($ue*0x9E3779B9);while($jf){$vb=$jf>>2&3;for($Yd=$A;$Yd>0;$Yd--){$eg=$V[$Yd-1];$Dd=xxtea_mx($eg,$dg,$jf,$v[$Yd&3^$vb]);$dg=int32($V[$Yd]-$Dd);$V[$Yd]=$dg;}$eg=$V[$A];$Dd=xxtea_mx($eg,$dg,$jf,$v[$Yd&3^$vb]);$dg=int32($V[0]-$Dd);$V[0]=$dg;$jf=int32($jf-0x9E3779B9);}return
long2str($V,true);}$G=array();if($_COOKIE["adminer_permanent"]){foreach(explode(" ",$_COOKIE["adminer_permanent"])as$W){list($v)=explode(":",$W);$G[$v]=$W;}}function
add_invalid_login(){$va=get_temp_dir()."/adminer.invalid";foreach(glob("$va*")?:array($va)as$l){$n=file_open_lock($l);if($n)break;}if(!$n)$n=file_open_lock("$va-".rand_string());if(!$n)return;$Rc=unserialize(stream_get_contents($n));$rf=time();if($Rc){foreach($Rc
as$Sc=>$W){if($W[0]<$rf)unset($Rc[$Sc]);}}$Qc=&$Rc[adminer()->bruteForceKey()];if(!$Qc)$Qc=array($rf+30*60,0);$Qc[1]++;file_write_unlock($n,serialize($Rc));}function
check_invalid_login(array&$G){$Rc=array();foreach(glob(get_temp_dir()."/adminer.invalid*")as$l){$n=file_open_lock($l);if($n){$Rc=unserialize(stream_get_contents($n));file_unlock($n);break;}}$Qc=idx($Rc,adminer()->bruteForceKey(),array());$Hd=($Qc[1]>29?$Qc[0]-time():0);if($Hd>0)auth_error(lang_format(array('Too many unsuccessful logins, try again in %d minute.','Too many unsuccessful logins, try again in %d minutes.'),ceil($Hd/60)),$G);}$na=$_POST["auth"];if($na){session_regenerate_id();$Y=$na["driver"];$N=$na["server"];$U=$na["username"];$F=(string)$na["password"];$g=$na["db"];set_password($Y,$N,$U,$F);$_SESSION["db"][$Y][$N][$U][$g]=true;if($na["permanent"]){$v=implode("-",array_map('base64_encode',array($Y,$N,$U,$g)));$re=adminer()->permanentLogin(true);$G[$v]="$v:".base64_encode($re?encrypt_string($F,$re):"");cookie("adminer_permanent",implode(" ",$G));}if(count($_POST)==1||DRIVER!=$Y||SERVER!=$N||$_GET["username"]!==$U||DB!=$g)redirect(auth_url($Y,$N,$U,$g));}elseif($_POST["logout"]&&(!$_SESSION["token"]||verify_token())){foreach(array("pwds","db","dbs","queries")as$v)set_session($v,null);unset_permanent($G);redirect(substr(preg_replace('~\b(username|db|ns)=[^&]*&~','',ME),0,-1),'Logout successful.'.' '.'Thanks for using Adminer, consider <a href="https://www.adminer.org/en/donation/">donating</a>.');}elseif($G&&!$_SESSION["pwds"]){session_regenerate_id();$re=adminer()->permanentLogin();foreach($G
as$v=>$W){list(,$Ia)=explode(":",$W);list($Y,$N,$U,$g)=array_map('base64_decode',explode("-",$v));set_password($Y,$N,$U,decrypt_string(base64_decode($Ia),$re));$_SESSION["db"][$Y][$N][$U][$g]=true;}}function
unset_permanent(array&$G){foreach($G
as$v=>$W){list($Y,$N,$U,$g)=array_map('base64_decode',explode("-",$v));if($Y==DRIVER&&$N==SERVER&&$U==$_GET["username"]&&$g==DB)unset($G[$v]);}cookie("adminer_permanent",implode(" ",$G));}function
auth_error($i,array&$G){$Se=session_name();if(isset($_GET["username"])){header("HTTP/1.1 403 Forbidden");if(($_COOKIE[$Se]||$_GET[$Se])&&!$_SESSION["token"])$i='Session expired, please login again.';else{restart_session();add_invalid_login();$F=get_password();if($F!==null){if($F===false)$i
.=($i?'<br>':'').sprintf('Master password expired. <a href="https://www.adminer.org/en/extension/"%s>Implement</a> %s method to make it permanent.',target_blank(),'<code>permanentLogin()</code>');set_password(DRIVER,SERVER,$_GET["username"],null);}unset_permanent($G);}}if(!$_COOKIE[$Se]&&$_GET[$Se]&&ini_bool("session.use_only_cookies"))$i='Session support must be enabled.';$be=session_get_cookie_params();cookie("adminer_key",($_COOKIE["adminer_key"]?:rand_string()),$be["lifetime"]);if(!$_SESSION["token"])$_SESSION["token"]=rand(1,1e6);page_header('Login',$i,null);echo"<form action='' method='post'>\n","<div>";if(hidden_fields($_POST,array("auth")))echo"<p class='message'>".'The action will be performed after successful login with the same credentials.'."\n";echo"</div>\n";adminer()->loginForm();echo"</form>\n";page_footer("auth");exit;}if(isset($_GET["username"])&&!class_exists('Adminer\Db')){unset($_SESSION["pwds"][DRIVER]);unset_permanent($G);page_header('No extension',sprintf('None of the supported PHP extensions (%s) are available.',implode(", ",Driver::$extensions)),false);page_footer("auth");exit;}$e='';if(isset($_GET["username"])&&is_string(get_password())){list(,$ke)=host_port(SERVER);if(preg_match('~^\s*([-+]?\d+)~',$ke,$z)&&($z[1]<1024||$z[1]>65535))auth_error('Connecting to privileged ports is not allowed.',$G);check_invalid_login($G);$ab=adminer()->credentials();$e=Driver::connect($ab[0],$ab[1],$ab[2]);if(is_object($e)){Db::$instance=$e;Driver::$instance=new
Driver($e);if($e->flavor)save_settings(array("vendor-".DRIVER."-".SERVER=>get_driver(DRIVER)));}}$kd=null;if(!is_object($e)||($kd=adminer()->login($_GET["username"],get_password()))!==true){$i=(is_string($e)?nl_br(h($e)):(is_string($kd)?$kd:'Invalid credentials.')).(preg_match('~^ | $~',get_password())?'<br>'.'There is a space in the input password which might be the cause.':'');auth_error($i,$G);}if($_POST["logout"]&&$_SESSION["token"]&&!verify_token()){page_header('Logout','Invalid CSRF token. Send the form again.');page_footer("db");exit;}if(!$_SESSION["token"])$_SESSION["token"]=rand(1,1e6);stop_session(true);if($na&&$_POST["token"])$_POST["token"]=get_token();$i='';if($_POST){if(!verify_token()){$Lc="max_input_vars";$vd=ini_get($Lc);if(extension_loaded("suhosin")){foreach(array("suhosin.request.max_vars","suhosin.post.max_vars")as$v){$W=ini_get($v);if($W&&(!$vd||$W<$vd)){$Lc=$v;$vd=$W;}}}$i=(!$_POST["token"]&&$vd?sprintf('Maximum number of allowed fields exceeded. Please increase %s.',"'$Lc'"):'Invalid CSRF token. Send the form again.'.' '.'If you did not send this request from Adminer then close this page.');}}elseif($_SERVER["REQUEST_METHOD"]=="POST"){$i=sprintf('Too big POST data. Reduce the data or increase the %s configuration directive.',"'post_max_size'");if(isset($_GET["sql"]))$i
.=' '.'You can upload a big SQL file via FTP and import it from server.';}function
doc_link(array$fe,$of=""){return"";}function
email_header($vc){return"=?UTF-8?B?".base64_encode($vc)."?=";}function
send_mail($zb,$hf,$_,$ic="",array$Wb=array()){$Gb=PHP_EOL;$_=str_replace("\n",$Gb,wordwrap(str_replace("\r","","$_\n")));$Aa=uniqid("boundary");$ma="";foreach((array)$Wb["error"]as$v=>$W){if(!$W)$ma
.="--$Aa$Gb"."Content-Type: ".str_replace("\n","",$Wb["type"][$v]).$Gb."Content-Disposition: attachment; filename=\"".preg_replace('~["\n]~','',$Wb["name"][$v])."\"$Gb"."Content-Transfer-Encoding: base64$Gb$Gb".chunk_split(base64_encode(file_get_contents($Wb["tmp_name"][$v])),76,$Gb).$Gb;}$xa="";$wc="Content-Type: text/plain; charset=utf-8$Gb"."Content-Transfer-Encoding: 8bit";if($ma){$ma
.="--$Aa--$Gb";$xa="--$Aa$Gb$wc$Gb$Gb";$wc="Content-Type: multipart/mixed; boundary=\"$Aa\"";}$wc
.=$Gb."MIME-Version: 1.0$Gb"."X-Mailer: Adminer Editor".($ic?$Gb."From: ".str_replace("\n","",$ic):"");return
mail($zb,email_header($hf),$xa.$_.$ma,$wc);}function
like_bool(array$j){return
preg_match("~bool|(tinyint|bit)\\(1\\)~",$j["full_type"]);}connection()->select_db(adminer()->database());adminer()->afterConnect();add_driver(DRIVER,'Login');if(isset($_GET["select"])&&($_POST["edit"]||$_POST["clone"])&&!$_POST["save"])$_GET["edit"]=$_GET["select"];if(isset($_GET["download"])){$a=$_GET["download"];$k=fields($a);header("Content-Type: application/octet-stream");header("Content-Disposition: attachment; filename=".friendly_url("$a-".implode("_",$_GET["where"])).".".friendly_url($_GET["field"]));$M=array(idf_escape($_GET["field"]));$I=driver()->select($a,$M,array(where($_GET,$k)),$M);$K=($I?$I->fetch_row():array());echo
driver()->value($K[0],$k[$_GET["field"]]);exit;}elseif(isset($_GET["edit"])){$a=$_GET["edit"];$k=fields($a);$Z=(isset($_GET["select"])?($_POST["check"]&&count($_POST["check"])==1?where_check($_POST["check"][0],$k):""):where($_GET,$k));$Mf=(isset($_GET["select"])?$_POST["edit"]:$Z);foreach($k
as$B=>$j){if(!isset($j["privileges"][$Mf?"update":"insert"])||adminer()->fieldName($j)==""||$j["generated"])unset($k[$B]);}if($_POST&&!$i&&!isset($_GET["select"])){$y=$_POST["referer"];if($_POST["insert"])$y=($Mf?null:$_SERVER["REQUEST_URI"]);elseif(!preg_match('~^.+&select=.+$~',$y))$y=ME."select=".urlencode($a);$u=indexes($a);$Hf=unique_array($_GET["where"],$u);$xe="\nWHERE $Z";if(isset($_POST["delete"]))queries_redirect($y,'Item has been deleted.',driver()->delete($a,$xe,$Hf?0:1));else{$O=array();foreach($k
as$B=>$j){$W=process_input($j);if($W!==false&&$W!==null)$O[idf_escape($B)]=$W;}if($Mf){if(!$O)redirect($y);queries_redirect($y,'Item has been updated.',driver()->update($a,$O,$xe,$Hf?0:1));if(is_ajax()){page_headers();page_messages($i);exit;}}else{$I=driver()->insert($a,$O);$fd=($I?last_id($I):0);queries_redirect($y,sprintf('Item%s has been inserted.',($fd?" $fd":"")),$I);}}}$K=null;if($_POST["save"])$K=(array)$_POST["fields"];elseif($Z){$M=array();foreach($k
as$B=>$j){if(isset($j["privileges"]["select"])){$ka=($_POST["clone"]&&$j["auto_increment"]?"''":convert_field($j));$M[]=($ka?"$ka AS ":"").idf_escape($B);}}$K=array();if(!support("table"))$M=array("*");if($M){$I=driver()->select($a,$M,array($Z),$M,array(),(isset($_GET["select"])?2:1));if(!$I)$i=error();else{$K=$I->fetch_assoc();if(!$K)$K=false;}if(isset($_GET["select"])&&(!$K||$I->fetch_assoc()))$K=null;}}if(!support("table")&&!$k){if(!$Z){$I=driver()->select($a,array("*"),array(),array("*"));$K=($I?$I->fetch_assoc():false);if(!$K)$K=array(driver()->primary=>"");}if($K){foreach($K
as$v=>$W){if(!$Z)$K[$v]=null;$k[$v]=array("field"=>$v,"null"=>($v!=driver()->primary),"auto_increment"=>($v==driver()->primary));}}}edit_form($a,$k,$K,$Mf,$i);}elseif(isset($_GET["select"])){$a=$_GET["select"];$R=table_status1($a);$u=indexes($a);$k=fields($a);$gc=column_foreign_keys($a);$Md=$R["Oid"];$ca=get_settings("adminer_import");$Ge=array();$d=array();$Ke=array();$Ud=array();$pf="";foreach($k
as$v=>$j){$B=adminer()->fieldName($j);$Ed=html_entity_decode(strip_tags($B),ENT_QUOTES);if(isset($j["privileges"]["select"])&&$B!=""){$d[$v]=$Ed;if(is_shortable($j))$pf=adminer()->selectLengthProcess();}if(isset($j["privileges"]["where"])&&$B!="")$Ke[$v]=$Ed;if(isset($j["privileges"]["order"])&&$B!="")$Ud[$v]=$Ed;$Ge+=$j["privileges"];}list($M,$p)=adminer()->selectColumnsProcess($d,$u);$M=array_unique($M);$p=array_unique($p);$Tc=count($p)<count($M);$Z=adminer()->selectSearchProcess($k,$u);$D=adminer()->selectOrderProcess($k,$u);$w=adminer()->selectLimitProcess();if($_GET["val"]&&is_ajax()){header("Content-Type: text/plain; charset=utf-8");foreach($_GET["val"]as$If=>$K){$ka=convert_field($k[key($K)]);$M=array($ka?:idf_escape(key($K)));$Z[]=where_check($If,$k);$J=driver()->select($a,$M,$Z,$M);if($J)echo
first($J->fetch_row());}exit;}$pe=$Kf=array();foreach($u
as$t){if($t["type"]=="PRIMARY"){$pe=array_flip($t["columns"]);$Kf=($M?$pe:array());foreach($Kf
as$v=>$W){if(in_array(idf_escape($v),$M))unset($Kf[$v]);}break;}}if($Md&&!$pe){$pe=$Kf=array($Md=>0);$u[]=array("type"=>"PRIMARY","columns"=>array($Md));}if($_POST&&!$i){$bg=$Z;if(!$_POST["all"]&&is_array($_POST["check"])){$Ha=array();foreach($_POST["check"]as$Fa)$Ha[]=where_check($Fa,$k);$bg[]="((".implode(") OR (",$Ha)."))";}$bg=($bg?"\nWHERE ".implode(" AND ",$bg):"");if($_POST["export"]){save_settings(array("output"=>$_POST["output"],"format"=>$_POST["format"]),"adminer_import");dump_headers($a);adminer()->dumpTable($a,"");$ic=($M?implode(", ",$M):"*").convert_fields($d,$k,$M)."\nFROM ".table($a);$oc=($p&&$Tc?"\nGROUP BY ".implode(", ",$p):"").($D?"\nORDER BY ".implode(", ",$D):"");$H="SELECT $ic$bg$oc";if(is_array($_POST["check"])&&!$pe){$Gf=array();foreach($_POST["check"]as$W)$Gf[]="(SELECT".limit($ic,"\nWHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check($W,$k).$oc,1).")";$H=implode(" UNION ALL ",$Gf);}adminer()->dumpData($a,"table",$H);adminer()->dumpFooter();exit;}if(!adminer()->selectEmailProcess($Z,$gc)){if($_POST["save"]||$_POST["delete"]){$I=true;$da=0;$O=array();if(!$_POST["delete"]){foreach($_POST["fields"]as$B=>$W){$W=process_input($k[$B]);if($W!==null&&($_POST["clone"]||$W!==false))$O[idf_escape($B)]=($W!==false?$W:idf_escape($B));}}if($_POST["delete"]||$O){$H=($_POST["clone"]?"INTO ".table($a)." (".implode(", ",array_keys($O)).")\nSELECT ".implode(", ",$O)."\nFROM ".table($a):"");if($_POST["all"]||($pe&&is_array($_POST["check"]))||$Tc){$I=($_POST["delete"]?driver()->delete($a,$bg):($_POST["clone"]?queries("INSERT $H$bg".driver()->insertReturning($a)):driver()->update($a,$O,$bg)));$da=connection()->affected_rows;if(is_object($I))$da+=$I->num_rows;}else{foreach((array)$_POST["check"]as$W){$ag="\nWHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check($W,$k);$I=($_POST["delete"]?driver()->delete($a,$ag,1):($_POST["clone"]?queries("INSERT".limit1($a,$H,$ag)):driver()->update($a,$O,$ag,1)));if(!$I)break;$da+=connection()->affected_rows;}}}$_=lang_format(array('%d item has been affected.','%d items have been affected.'),$da);if($_POST["clone"]&&$I&&$da==1){$fd=last_id($I);if($fd)$_=sprintf('Item%s has been inserted.'," $fd");}queries_redirect(remove_from_uri($_POST["all"]&&$_POST["delete"]?"page":""),$_,$I);if(!$_POST["delete"]){$me=(array)$_POST["fields"];edit_form($a,array_intersect_key($k,$me),$me,!$_POST["clone"],$i);page_footer();exit;}}elseif(!$_POST["import"]){if(!$_POST["val"])$i='Ctrl+click on a value to modify it.';else{$I=true;$da=0;foreach($_POST["val"]as$If=>$K){$O=array();foreach($K
as$v=>$W){$v=bracket_escape($v,true);$O[idf_escape($v)]=(preg_match('~char|text~',$k[$v]["type"])||$W!=""?adminer()->processInput($k[$v],$W):"NULL");}$I=driver()->update($a,$O," WHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check($If,$k),($Tc||$pe?0:1)," ");if(!$I)break;$da+=connection()->affected_rows;}queries_redirect(remove_from_uri(),lang_format(array('%d item has been affected.','%d items have been affected.'),$da),$I);}}elseif(!is_string($Vb=get_file("csv_file",true)))$i=upload_error($Vb);elseif(!preg_match('~~u',$Vb))$i='File must be in UTF-8 encoding.';else{save_settings(array("output"=>$ca["output"],"format"=>$_POST["separator"]),"adminer_import");$I=true;$Pa=array_keys($k);preg_match_all('~(?>"[^"]*"|[^"\r\n]+)+~',$Vb,$pd);$da=count($pd[0]);driver()->begin();$Pe=($_POST["separator"]=="csv"?",":($_POST["separator"]=="tsv"?"\t":";"));$L=array();foreach($pd[0]as$v=>$W){preg_match_all("~((?>\"[^\"]*\")+|[^$Pe]*)$Pe~",$W.$Pe,$qd);if(!$v&&!array_diff($qd[1],$Pa)){$Pa=$qd[1];$da--;}else{$O=array();foreach($qd[1]as$q=>$Ma)$O[idf_escape($Pa[$q])]=($Ma==""&&$k[$Pa[$q]]["null"]?"NULL":q(preg_match('~^".*"$~s',$Ma)?str_replace('""','"',substr($Ma,1,-1)):$Ma));$L[]=$O;}}$I=(!$L||driver()->insertUpdate($a,$L,$pe));if($I)driver()->commit();queries_redirect(remove_from_uri("page"),lang_format(array('%d row has been imported.','%d rows have been imported.'),$da),$I);driver()->rollback();}}}$mf=adminer()->tableName($R);if(is_ajax()){page_headers();ob_start();}else
page_header('Select'.": $mf",$i);$O=null;if(isset($Ge["insert"])||!support("table")){$be=array();foreach((array)$_GET["where"]as$W){if(isset($gc[$W["col"]])&&count($gc[$W["col"]])==1&&($W["op"]=="="||(!$W["op"]&&(is_array($W["val"])||!preg_match('~[_%]~',$W["val"])))))$be["set"."[".bracket_escape($W["col"])."]"]=$W["val"];}$O=$be?"&".http_build_query($be):"";}adminer()->selectLinks($R,$O);if(!$d&&support("table"))echo"<p class='error'>".'Unable to select the table'.($k?".":": ".error())."\n";else{echo"<form action='' id='form'>\n","<div style='display: none;'>";hidden_fields_get();echo(DB!=""?input_hidden("db",DB).(isset($_GET["ns"])?input_hidden("ns",$_GET["ns"]):""):""),input_hidden("select",$a),"</div>\n";adminer()->selectColumnsPrint($M,$d);adminer()->selectSearchPrint($Z,$Ke,$u);adminer()->selectOrderPrint($D,$Ud,$u);adminer()->selectLimitPrint($w);adminer()->selectLengthPrint($pf);adminer()->selectActionPrint($u);echo"</form>\n";$E=$_GET["page"];$m=null;if($E=="last"){$m=get_val(count_rows($a,$Z,$Tc,$p));$E=floor(max(0,intval($m)-1)/$w);}$Le=$M;$nc=$p;if(!$Le){$Le[]="*";$Wa=convert_fields($d,$k,$M);if($Wa)$Le[]=substr($Wa,2);}foreach($M
as$v=>$W){$j=$k[idf_unescape($W)];if($j&&($ka=convert_field($j)))$Le[$v]="$ka AS $W";}if(!$Tc&&$Kf){foreach($Kf
as$v=>$W){$Le[]=idf_escape($v);if($nc)$nc[]=idf_escape($v);}}$I=driver()->select($a,$Le,$Z,$nc,$D,$w,$E,true);if(!$I)echo"<p class='error'>".error()."\n";else{if(JUSH=="mssql"&&$E)$I->seek($w*$E);$Ab=array();echo"<form action='' method='post' enctype='multipart/form-data'>\n";$L=array();while($K=$I->fetch_assoc()){if($E&&JUSH=="oracle")unset($K["RNUM"]);$L[]=$K;}if($_GET["page"]!="last"&&$w&&$p&&$Tc&&JUSH=="sql")$m=get_val(" SELECT FOUND_ROWS()");if(!$L)echo"<p class='message'>".'No rows.'."\n";else{$ua=adminer()->backwardKeys($a,$mf);echo"<div class='scrollable'>","<table id='table' class='nowrap checkable odds'>",script("mixin(qs('#table'), {onclick: tableClick, ondblclick: partialArg(tableClick, true), onkeydown: editingKeydown});"),"<thead><tr>".(!$p&&$M?"":"<td><input type='checkbox' id='all-page' class='jsonly'>".script("qs('#all-page').onclick = partial(formCheck, /check/);","")." <a href='".h($_GET["modify"]?remove_from_uri("modify"):$_SERVER["REQUEST_URI"]."&modify=1")."'>".'Modify'."</a>");$Fd=array();$kc=array();reset($M);$ze=1;foreach($L[0]as$v=>$W){if(!isset($Kf[$v])){$W=idx($_GET["columns"],key($M))?:array();$j=$k[$M?($W?$W["col"]:current($M)):$v];$B=($j?adminer()->fieldName($j,$ze):($W["fun"]?"*":h($v)));if($B!=""){$ze++;$Fd[$v]=$B;$c=idf_escape($v);$Bc=remove_from_uri('(order|desc)[^=]*|page').'&order%5B0%5D='.urlencode($v);$mb="&desc%5B0%5D=1";echo"<th id='th[".h(bracket_escape($v))."]'>".script("mixin(qsl('th'), {onmouseover: partial(columnMouse), onmouseout: partial(columnMouse, ' hidden')});","");$jc=apply_sql_function($W["fun"],$B);$Xe=isset($j["privileges"]["order"])||$jc;echo($Xe?"<a href='".h($Bc.($D[0]==$c||$D[0]==$v?$mb:''))."'>$jc</a>":$jc),"<span class='column hidden'>";if($Xe)echo"<a href='".h($Bc.$mb)."' title='".'descending'."' class='text'> ‚Üì</a>";if(!$W["fun"]&&isset($j["privileges"]["where"]))echo'<a href="#fieldset-search" title="'.'Search'.'" class="text jsonly"> =</a>',script("qsl('a').onclick = partial(selectSearch, '".js_escape($v)."');");echo"</span>";}$kc[$v]=$W["fun"];next($M);}}$id=array();if($_GET["modify"]){foreach($L
as$K){foreach($K
as$v=>$W)$id[$v]=max($id[$v],min(40,strlen(utf8_decode($W))));}}echo($ua?"<th>".'Relations':"")."</thead>\n";if(is_ajax())ob_end_clean();foreach(adminer()->rowDescriptions($L,$gc)as$A=>$K){$Hf=unique_array($L[$A],$u);if(!$Hf){$Hf=array();reset($M);foreach($L[$A]as$v=>$W){if(!preg_match('~^(COUNT|AVG|GROUP_CONCAT|MAX|MIN|SUM)\(~',current($M)))$Hf[$v]=$W;next($M);}}$If="";foreach($Hf
as$v=>$W){$j=(array)$k[$v];if((JUSH=="sql"||JUSH=="pgsql")&&preg_match('~char|text|enum|set~',$j["type"])&&strlen($W)>64){$v=(strpos($v,'(')?$v:idf_escape($v));$v="MD5(".(JUSH!='sql'||preg_match("~^utf8~",$j["collation"])?$v:"CONVERT($v USING ".charset(connection()).")").")";$W=md5($W);}$If
.="&".($W!==null?urlencode("where[".bracket_escape($v)."]")."=".urlencode($W===false?"f":$W):"null%5B%5D=".urlencode($v));}echo"<tr>".(!$p&&$M?"":"<td>".checkbox("check[]",substr($If,1),in_array(substr($If,1),(array)$_POST["check"])).($Tc||information_schema(DB)?"":" <a href='".h(ME."edit=".urlencode($a).$If)."' class='edit'>".'edit'."</a>"));reset($M);foreach($K
as$v=>$W){if(isset($Fd[$v])){$c=current($M);$j=(array)$k[$v];$W=driver()->value($W,$j);if($W!=""&&(!isset($Ab[$v])||$Ab[$v]!=""))$Ab[$v]=(is_mail($W)?$Fd[$v]:"");$x="";if(is_blob($j)&&$W!="")$x=ME.'download='.urlencode($a).'&field='.urlencode($v).$If;if(!$x&&$W!==null){foreach((array)$gc[$v]as$fc){if(count($gc[$v])==1||end($fc["source"])==$v){$x="";foreach($fc["source"]as$q=>$Ye)$x
.=where_link($q,$fc["target"][$q],$L[$A][$Ye]);$x=($fc["db"]!=""?preg_replace('~([?&]db=)[^&]+~','\1'.urlencode($fc["db"]),ME):ME).'select='.urlencode($fc["table"]).$x;if($fc["ns"])$x=preg_replace('~([?&]ns=)[^&]+~','\1'.urlencode($fc["ns"]),$x);if(count($fc["source"])==1)break;}}}if($c=="COUNT(*)"){$x=ME."select=".urlencode($a);$q=0;foreach((array)$_GET["where"]as$V){if(!array_key_exists($V["col"],$Hf))$x
.=where_link($q++,$V["col"],$V["val"],$V["op"]);}foreach($Hf
as$Yc=>$V)$x
.=where_link($q++,$Yc,$V);}$Cc=select_value($W,$x,$j,$pf);$r=h("val[$If][".bracket_escape($v)."]");$ne=idx(idx($_POST["val"],$If),bracket_escape($v));$xb=!is_array($K[$v])&&is_utf8($Cc)&&$L[$A][$v]==$K[$v]&&!$kc[$v]&&!$j["generated"];$T=(preg_match('~^(AVG|MIN|MAX)\((.+)\)~',$c,$z)?$k[idf_unescape($z[2])]["type"]:$j["type"]);$of=preg_match('~text|json|lob~',$T);$Uc=preg_match(number_type(),$T)||preg_match('~^(CHAR_LENGTH|ROUND|FLOOR|CEIL|TIME_TO_SEC|COUNT|SUM)\(~',$c);echo"<td id='$r'".($Uc&&($W===null||is_numeric(strip_tags($Cc))||$T=="money")?" class='number'":"");if(($_GET["modify"]&&$xb&&$W!==null)||$ne!==null){$qc=h($ne!==null?$ne:$K[$v]);echo">".($of?"<textarea name='$r' cols='30' rows='".(substr_count($K[$v],"\n")+1)."'>$qc</textarea>":"<input name='$r' value='$qc' size='$id[$v]'>");}else{$ld=strpos($Cc,"<i>‚Ä¶</i>");echo" data-text='".($ld?2:($of?1:0))."'".($xb?"":" data-warning='".h('Use edit link to modify this value.')."'").">$Cc";}}next($M);}if($ua)echo"<td>";adminer()->backwardKeysPrint($ua,$L[$A]);echo"</tr>\n";}if(is_ajax())exit;echo"</table>\n","</div>\n";}if(!is_ajax()){if($L||$E){$Lb=true;if($_GET["page"]!="last"){if(!$w||(count($L)<$w&&($L||!$E)))$m=($E?$E*$w:0)+count($L);elseif(JUSH!="sql"||!$Tc){$m=($Tc?false:found_rows($R,$Z));if(intval($m)<max(1e4,2*($E+1)*$w))$m=first(slow_query(count_rows($a,$Z,$Tc,$p)));else$Lb=false;}}$Zd=($w&&($m===false||$m>$w||$E));if($Zd)echo(($m===false?count($L)+1:$m-$E*$w)>$w?'<p><a href="'.h(remove_from_uri("page")."&page=".($E+1)).'" class="loadmore">'.'Load more data'.'</a>'.script("qsl('a').onclick = partial(selectLoadMore, $w, '".'Loading'."‚Ä¶');",""):''),"\n";echo"<div class='footer'><div>\n";if($Zd){$td=($m===false?$E+(count($L)>=$w?2:1):floor(($m-1)/$w));echo"<fieldset>";if(JUSH!="simpledb"){echo"<legend><a href='".h(remove_from_uri("page"))."'>".'Page'."</a></legend>",script("qsl('a').onclick = function () { pageClick(this.href, +prompt('".'Page'."', '".($E+1)."')); return false; };"),pagination(0,$E).($E>5?" ‚Ä¶":"");for($q=max(1,$E-4);$q<min($td,$E+5);$q++)echo
pagination($q,$E);if($td>0)echo($E+5<$td?" ‚Ä¶":""),($Lb&&$m!==false?pagination($td,$E):" <a href='".h(remove_from_uri("page")."&page=last")."' title='~$td'>".'last'."</a>");}else
echo"<legend>".'Page'."</legend>",pagination(0,$E).($E>1?" ‚Ä¶":""),($E?pagination($E,$E):""),($td>$E?pagination($E+1,$E).($td>$E+1?" ‚Ä¶":""):"");echo"</fieldset>\n";}echo"<fieldset>","<legend>".'Whole result'."</legend>";$rb=($Lb?"":"~ ").$m;$Pd="const checked = formChecked(this, /check/); selectCount('selected', this.checked ? '$rb' : checked); selectCount('selected2', this.checked || !checked ? '$rb' : checked);";echo
checkbox("all",1,0,($m!==false?($Lb?"":"~ ").lang_format(array('%d row','%d rows'),$m):""),$Pd)."\n","</fieldset>\n";if(adminer()->selectCommandPrint())echo'<fieldset',($_GET["modify"]?'':' class="jsonly"'),'><legend>Modify</legend><div>
<input type="submit" value="Save"',($_GET["modify"]?'':' title="'.'Ctrl+click on a value to modify it.'.'"'),'>
</div></fieldset>
<fieldset><legend>Selected <span id="selected"></span></legend><div>
<input type="submit" name="edit" value="Edit">
<input type="submit" name="clone" value="Clone">
<input type="submit" name="delete" value="Delete">',confirm(),'</div></fieldset>
';$hc=adminer()->dumpFormat();foreach((array)$_GET["columns"]as$c){if($c["fun"]){unset($hc['sql']);break;}}if($hc){print_fieldset("export",'Export'." <span id='selected2'></span>");$Xd=adminer()->dumpOutput();echo($Xd?html_select("output",$Xd,$ca["output"])." ":""),html_select("format",$hc,$ca["format"])," <input type='submit' name='export' value='".'Export'."'>\n","</div></fieldset>\n";}adminer()->selectEmailPrint(array_filter($Ab,'strlen'),$d);echo"</div></div>\n";}if(adminer()->selectImportPrint())echo"<p>","<a href='#import'>".'Import'."</a>",script("qsl('a').onclick = partial(toggle, 'import');",""),"<span id='import'".($_POST["import"]?"":" class='hidden'").">: ",file_input("<input type='file' name='csv_file'> ".html_select("separator",array("csv"=>"CSV,","csv;"=>"CSV;","tsv"=>"TSV"),$ca["format"])." <input type='submit' name='import' value='".'Import'."'>"),"</span>";echo
input_token(),"</form>\n",(!$p&&$M?"":script("tableCheck();"));}}}if(is_ajax()){ob_end_clean();exit;}}elseif(isset($_GET["script"])){if($_GET["script"]=="kill")connection()->query("KILL ".number($_POST["kill"]));elseif(list($Q,$r,$B)=adminer()->_foreignColumn(column_foreign_keys($_GET["source"]),$_GET["field"])){$w=11;$I=connection()->query("SELECT $r, $B FROM ".table($Q)." WHERE ".(preg_match('~^[0-9]+$~',$_GET["value"])?"$r = $_GET[value] OR ":"")."$B LIKE ".q("$_GET[value]%")." ORDER BY 2 LIMIT $w");for($q=1;($K=$I->fetch_row())&&$q<$w;$q++)echo"<a href='".h(ME."edit=".urlencode($Q)."&where".urlencode("[".bracket_escape(idf_unescape($r))."]")."=".urlencode($K[0]))."'>".h($K[1])."</a><br>\n";if($K)echo"...\n";}exit;}else{page_header('Server',"",false);if(adminer()->homepage()){echo"<form action='' method='post'>\n","<p>".'Search data in tables'.": <input type='search' name='query' value='".h($_POST["query"])."'> <input type='submit' value='".'Search'."'>\n";if($_POST["query"]!="")search_tables();echo"<div class='scrollable'>\n","<table class='nowrap checkable odds'>\n",script("mixin(qsl('table'), {onclick: tableClick, ondblclick: partialArg(tableClick, true)});"),'<thead><tr class="wrap">','<td><input id="check-all" type="checkbox" class="jsonly">'.script("qs('#check-all').onclick = partial(formCheck, /^tables\[/);",""),'<th>'.'Table','<td>'.'Rows',"</thead>\n";foreach(table_status()as$Q=>$K){$B=adminer()->tableName($K);if($B!=""){echo'<tr><td>'.checkbox("tables[]",$Q,in_array($Q,(array)$_POST["tables"],true)),"<th><a href='".h(ME).'select='.urlencode($Q)."'>$B</a>";$W=format_number($K["Rows"]);echo"<td align='right'><a href='".h(ME."edit=").urlencode($Q)."'>".($K["Engine"]=="InnoDB"&&$W?"~ $W":$W)."</a>";}}echo"</table>\n","</div>\n","</form>\n",script("tableCheck();");adminer()->pluginsLinks();}}page_footer();