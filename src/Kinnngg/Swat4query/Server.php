<?php

namespace Kinnngg\Swat4query;

/**
* The Server Class
*/
class Server
{
  protected $serverIp;
  protected $serverQPort;
  public $option;
  
  /**
   * Initialises the Oject
   * 
   * @param string $serverIp    Server ip address to query
   * @param int/string $serverQPort Server query port
   */
  public function __construct($serverIp,$serverQPort=10481)
  {
    $this->serverIp = $serverIp;
    $this->serverQPort = $serverQPort;
    $this->option = [];
  }

  public function __toString()
  {
    return json_encode($this->option);
  }

  public function query($value=null)
  {
    // Retrieving data from the server
    $sock=fsockopen("udp://".$this->serverIp,$this->serverQPort,$errno,$errstr,2);
    if(!$sock){ echo "$errstr ($errno)<br/>\n"; exit; }
     fputs($sock,"\\status\\"); $gotfinal=False; $data="";
     socket_set_timeout($sock,0,200000);
     $starttime=Time();
     while(!($gotfinal==True||feof($sock))) {
      if(($buf=fgetc($sock))==FALSE) usleep(100);
      $data.=$buf;
      if(strpos($data,"final\\")!=False) $gotfinal=True;
      if((Time() - $starttime)>2) { 
      $gotfinal=True;
       }
     }
     fclose($sock);
     $chunks=explode('\\',$data);

     // Correcting data
     if (self::GetItemInfo("hostname",$chunks) == "-") {
      $this->option['hostname']= "...server is reloading or offline";
      } else {
      $this->option['hostname']=self::FontCodes(self::GetItemInfo("hostname",$chunks));
     }
     $this->option['password']=(self::GetItemInfo("password",$chunks)==0)?"No":"Yes";
     $this->option['patch']=self::GetItemInfo("gamever",$chunks);
      $mods_tmp=self::GetItemInfo("gamevariant",$chunks);
     $this->option['mods']=($mods_tmp=="SWAT 4")?"None":$mods_tmp;
     $this->option['map']=self::GetItemInfo("mapname",$chunks);
     $this->option['gametype']=self::GetItemInfo("gametype",$chunks);
     $this->option['players_current']=self::GetItemInfo("numplayers",$chunks);
     $this->option['players_max']=self::GetItemInfo("maxplayers",$chunks);
     $this->option['statsenabled']=(self::GetItemInfo("statsenabled",$chunks)==0)?"No":"Yes";
     $this->option['swatwon']=self::GetItemInfo("swatwon",$chunks);
     $this->option['suspectswon']=self::GetItemInfo("suspectswon",$chunks);

     $this->option['round']=self::GetItemInfo("round",$chunks);
     $this->option['numrounds']=self::GetItemInfo("numrounds",$chunks);
     $this->option['suspectsscore']=self::GetItemInfo("suspectsscore",$chunks);
     $this->option['swatscore']=self::GetItemInfo("swatscore",$chunks);
     $this->option['timeleft']=self::GetItemInfo("timeleft",$chunks);
     $this->option['nextmap']=self::GetItemInfo("nextmap",$chunks);

      $this->option['players']=array();
     for ($i=0;$i<$this->option['players_current'];$i++) {
      $nametmp=self::FixNickname(self::GetItemInfo("player_".$i,$chunks));
      $nametmp=self::FontCodes($nametmp);
      if($nametmp!="-") {
       $this->option['players'][$i]['name']=$nametmp;
       $this->option['players'][$i]['score']=self::GetItemInfo("score_".$i,$chunks);
       $this->option['players'][$i]['ping']=self::GetItemInfo("ping_".$i,$chunks);
       $this->option['players'][$i]['ip']=self::GetItemInfo("playerip_".$i,$chunks);
       $this->option['players'][$i]['team']=self::GetItemInfo("team_".$i,$chunks);
       $this->option['players'][$i]['kills']=self::GetItemInfo("kills_".$i,$chunks);
       $this->option['players'][$i]['tkills']=self::GetItemInfo("tkills_".$i,$chunks);
       $this->option['players'][$i]['deaths']=self::GetItemInfo("deaths_".$i,$chunks);
       $this->option['players'][$i]['arrests']=self::GetItemInfo("arrests_".$i,$chunks);
       $this->option['players'][$i]['arrested']=self::GetItemInfo("arrested_".$i,$chunks);
       $this->option['players'][$i]['vipe']=self::GetItemInfo("vipescaped_".$i,$chunks);
       $this->option['players'][$i]['vipkv']=self::GetItemInfo("validvipkills_".$i,$chunks);
       $this->option['players'][$i]['vipki']=self::GetItemInfo("invalidvipkills_".$i,$chunks);
       $this->option['players'][$i]['vipa']=self::GetItemInfo("arrestedvip_".$i,$chunks);
       $this->option['players'][$i]['vipua']=self::GetItemInfo("unarrestedvip_".$i,$chunks);
       $this->option['players'][$i]['bombsd']=self::GetItemInfo("bombsdiffused_".$i,$chunks);
       $this->option['players'][$i]['rdobjective']=self::GetItemInfo("rdcrybaby_".$i,$chunks);
       $this->option['players'][$i]['sgobjective']=self::GetItemInfo("sgcrybaby_".$i,$chunks);
       $this->option['players'][$i]['sge']=self::GetItemInfo("escapedcase_".$i,$chunks);
       $this->option['players'][$i]['sgk']=self::GetItemInfo("killedcase_".$i,$chunks);
      }
      else {
       $this->option['players'][$i]['name']="-";
       $this->option['players'][$i]['score']="-";
       $this->option['players'][$i]['ping']="-";
       $this->option['players'][$i]['ip']="-";
       $this->option['players'][$i]['team']="-";
       $this->option['players'][$i]['kills']="-";
       $this->option['players'][$i]['tkills']="-";
       $this->option['players'][$i]['deaths']="-";
       $this->option['players'][$i]['arrests']="-";
       $this->option['players'][$i]['arrested']="-";
       $this->option['players'][$i]['vipe']="-";
       $this->option['players'][$i]['vipkv']="-";
       $this->option['players'][$i]['vipki']="-";
       $this->option['players'][$i]['vipa']="-";
       $this->option['players'][$i]['vipua']="-";
       $this->option['players'][$i]['bombsd']="-";
       $this->option['players'][$i]['rdobjective']="-";
       $this->option['players'][$i]['sgobjective']="-";
       $this->option['players'][$i]['sge']="-";
       $this->option['players'][$i]['sgk']="-";
      }
     }

     if((!IsSet($_GET['by']))||(!IsSet($_GET['sort']))) { $_by="score"; $_sort=-1;
     } else { $_by=$_GET['by']; $_sort=($_GET['sort']=="ASC")?1:-1; }
     $_sorting=array("name"=>1,"score"=>-1,"ping"=>1,"ip"=>1,"kills"=>1,"tkills"=>1,"deaths"=>1,"arrests"=>1,"arrested"=>1,"vipe"=>1,"vipkv"=>1,"vipki"=>1,"vipa"=>1,"vipua"=>1,"bombsd"=>1,"rdobjective"=>1,"sgobjective"=>1,"sge"=>1,"sgk"=>1);
     $_sorting[$_by]=$_sort * -1;
      $usortopt=($_sort==1)?"ASC":"DESC";
     @usort($this->option['players'],"self::SortPlayers_".$_by."_".$usortopt);

  }




  /**
   * A Helper Function.
   * Returns Item Info from chunk.
   * 
   * @param [type] $itemname   [description]
   * @param [type] $itemchunks [description]
   */
  public static function GetItemInfo($itemname, $itemchunks)
  {
    $retval = "-";
    for ($i=0;$i<count($itemchunks);$i++)
    if (strcasecmp($itemchunks[$i], $itemname) == 0) $retval = $itemchunks[$i+1];
    return  $retval;
  }

  /**
   * Helper Function
   * Convery SWAT4 Server font codes into html tags
   *
   * @param string $data
   * @return  string
   */
  
  public static function FontCodes($text,$advanced=TRUE,$charset='utf-8'){
    //special chars
    $text  = htmlspecialchars($text, ENT_QUOTES,$charset);
    /**
     * This array contains the main static bbcode
     * @var array $basic_bbcode
     */
    $basic_bbcode = array(
                '[b]', '[/b]',
                '[i]', '[/i]',
                '[u]', '[/u]',
                '[B]', '[/B]',
                '[I]', '[/I]',
                '[U]', '[/U]',
    );
    /**
     * This array contains the main static bbcode's html
     * @var array $basic_html
     */
    $basic_html = array(
                '<b>', '</b>',
                '<i>', '</i>',
                '<u>', '</u>',
                '<b>', '</b>',
                '<i>', '</i>',
                '<u>', '</u>',
    );
    /**
     *
     * Parses basic bbcode, used str_replace since seems to be the fastest
     */
    $text = str_replace($basic_bbcode, $basic_html, $text);
    //advanced BBCODE
    if ($advanced)
    {
      /**
       * This array contains the advanced static bbcode
       * @var array $advanced_bbcode
       */
      $advanced_bbcode = array(
                   '/\[c=([0-9a-fA-F]{6})\](.+)(\[\\c\])?/i',
      );
      /**
       * This array contains the advanced static bbcode's html
       * @var array $advanced_html
       */
      $advanced_html = array(
                   "<span style='color: #$1'>$2</span>",
                   );

      $text = htmlspecialchars(preg_replace($advanced_bbcode, $advanced_html,$text));
    }
    return $text;
  }

  /*public static function FontCodes($data) {
   $data=str_replace("[B]","[b]",$data);
   $data=str_replace("[U]","[u]",$data);
   $data=str_replace("[I]","[i]",$data);
   $data=str_replace("[C=","[c=",$data);
   preg_match_all("/\[([BbUuIi])\]/",$data,$bui_tags); $bui_count=count($bui_tags[1]);
   $color_count=substr_count($data,"[c=");
   if($color_count > 0) {
    if($bui_count > 0) {
     if($color_count==1) {
      for($i=$bui_count;$i>0;$i--) $data.="[/".$bui_tags[1][$i-1]."]";
      $data=preg_replace("/\[c=([0-9a-fA-F]{6})\]/","<span style=\"color: #\\1;\">",$data)."</span>";
     } else {
      $tag=array("b"=>0,"u"=>0,"i"=>0);
      $color=explode("[c=",$data);
       $datatmp="";
      for($i=1;$i<count($color);$i++) { $colortmp="[c=".$color[$i];
       if($tag['b']) { $colortmp=preg_replace("/(\[c=[0-9a-fA-F]{6}\])/","\\1[b]",$colortmp); }
       if($tag['u']) { $colortmp=preg_replace("/(\[c=[0-9a-fA-F]{6}\])/","\\1[u]",$colortmp); }
       if($tag['i']) { $colortmp=preg_replace("/(\[c=[0-9a-fA-F]{6}\])/","\\1[i]",$colortmp); }
       if(strpos($colortmp,"[b]")) { $colortmp.="[/b]"; $tag['b']=1; }
       if(strpos($colortmp,"[u]")) { $colortmp.="[/u]"; $tag['u']=1; }
       if(strpos($colortmp,"[i]")) { $colortmp.="[/i]"; $tag['i']=1; }
       $datatmp.=$colortmp;
      }
      $data=$datatmp."</span>";
      $data=preg_replace("/[c=([0-9a-fA-F]{6})\]/","</span><span style=\"color: #\\1;\">",$data);
      $data=preg_replace('/<\/span>/','',$data,1);
     }
    } else {
     $data=preg_replace("/\[c=([0-9a-fA-F]{6})\]/","<\/span><span style=\"color: #\\1;\">",$data);
     if(substr_count($data,"<span")) { $data=preg_replace('/[<\/span>]/','',$data,1); $data.="</span>"; }
    }
   } else {
    if($bui_count > 0) for($i=$bui_count;$i>0;$i--) $data.="[/".$bui_tags[1][$i-1]."]";
   }
   $data=preg_replace("/\[(\[bui\])\]|\[(\[bui\])\]/","<\\1\\2>",$data);
  return $data;
  }*/


  /**
   * Helper Function
   * Returns fixed names for html.
   * 
   * @param string $nick
   * @return  string
   */
  public static function FixNickname($nick) {
   $nick=str_replace('&','&amp;',$nick);
   $nick=str_replace('<','&lt;',$nick);
   $nick=str_replace('>','&gt;',$nick);
  return $nick;
  }

  /**
   * A Helper Function
   * This function will sort players.
   * 
   * @param [type] $a   [description]
   * @param [type] $b   [description]
   * @param [type] $co  [description]
   * @param [type] $jak [description]
   */
  public static function SortPlayers($a,$b,$co,$jak) {
   if($co=="name") {
    $a2=strtolower($a['name']);
    $b2=strtolower($b['name']);
    if($a2==$b2) return 0;
    if((($jak=="+")&&($a2>$b2))||(($jak=="-")&&($a2<$b2))) return 1; else return -1;
   } else {
    if($a[$co]==$b[$co]) return 0;
    if((($jak=="+")&&($a[$co]>$b[$co]))||(($jak=="-")&&($a[$co]<$b[$co]))) return 1; else return -1;
   }
  }

  /**
   * Helper functions
   * These functions can be used to sort out data ina given form.
   */
  public static function SortPlayers_name_ASC($a,$b) { return self::SortPlayers($a,$b,'name','+'); }
  public static function SortPlayers_name_DESC($a,$b) { return self::SortPlayers($a,$b,'name','-'); }
  public static function SortPlayers_score_ASC($a,$b) { return self::SortPlayers($a,$b,'score','+'); }
  public static function SortPlayers_score_DESC($a,$b) { return self::SortPlayers($a,$b,'score','-'); }
  public static function SortPlayers_ping_ASC($a,$b) { return self::SortPlayers($a,$b,'ping','+'); }
  public static function SortPlayers_ping_DESC($a,$b) { return self::SortPlayers($a,$b,'ping','-'); }
  public static function SortPlayers_kills_ASC($a,$b) { return self::SortPlayers($a,$b,'kills','+'); }
  public static function SortPlayers_kills_DESC($a,$b) { return self::SortPlayers($a,$b,'kills','-'); }
  public static function SortPlayers_tkills_ASC($a,$b) { return self::SortPlayers($a,$b,'tkills','+'); }
  public static function SortPlayers_tkills_DESC($a,$b) { return self::SortPlayers($a,$b,'tkills','-'); }
  public static function SortPlayers_deaths_ASC($a,$b) { return self::SortPlayers($a,$b,'deaths','+'); }
  public static function SortPlayers_deaths_DESC($a,$b) { return self::SortPlayers($a,$b,'deaths','-'); }
  public static function SortPlayers_arrests_ASC($a,$b) { return self::SortPlayers($a,$b,'arrests','+'); }
  public static function SortPlayers_arrests_DESC($a,$b) { return self::SortPlayers($a,$b,'arrests','-'); }
  public static function SortPlayers_arrested_ASC($a,$b) { return self::SortPlayers($a,$b,'arrested','+'); }
  public static function SortPlayers_arrested_DESC($a,$b) { return self::SortPlayers($a,$b,'arrested','-'); }
  public static function SortPlayers_vipe_ASC($a,$b) { return self::SortPlayers($a,$b,'vipe','+'); }
  public static function SortPlayers_vipe_DESC($a,$b) { return self::SortPlayers($a,$b,'vipe','-'); }
  public static function SortPlayers_vipkv_ASC($a,$b) { return self::SortPlayers($a,$b,'vipkv','+'); }
  public static function SortPlayers_vipkv_DESC($a,$b) { return self::SortPlayers($a,$b,'vipkv','-'); }
  public static function SortPlayers_vipki_ASC($a,$b) { return self::SortPlayers($a,$b,'vipki','+'); }
  public static function SortPlayers_vipki_DESC($a,$b) { return self::SortPlayers($a,$b,'vipki','-'); }
  public static function SortPlayers_vipa_ASC($a,$b) { return self::SortPlayers($a,$b,'vipa','+'); }
  public static function SortPlayers_vipa_DESC($a,$b) { return self::SortPlayers($a,$b,'vipa','-'); }
  public static function SortPlayers_vipua_ASC($a,$b) { return self::SortPlayers($a,$b,'vipua','+'); }
  public static function SortPlayers_vipua_DESC($a,$b) { return self::SortPlayers($a,$b,'vipua','-'); }
  public static function SortPlayers_bombsd_ASC($a,$b) { return self::SortPlayers($a,$b,'bombsd','+'); }
  public static function SortPlayers_bombsd_DESC($a,$b) { return self::SortPlayers($a,$b,'bombsd','-'); }
  public static function SortPlayers_rdobjective_ASC($a,$b) { return self::SortPlayers($a,$b,'rdobjective','+'); }
  public static function SortPlayers_rdobjective_DESC($a,$b) { return self::SortPlayers($a,$b,'rdobjective','-'); }
  public static function SortPlayers_sgobjective_ASC($a,$b) { return self::SortPlayers($a,$b,'sgobjective','+'); }
  public static function SortPlayers_sgobjective_DESC($a,$b) { return self::SortPlayers($a,$b,'sgobjective','-'); }
  public static function SortPlayers_sge_ASC($a,$b) { return self::SortPlayers($a,$b,'sge','+'); }
  public static function SortPlayers_sge_DESC($a,$b) { return self::SortPlayers($a,$b,'sge','-'); }
  public static function SortPlayers_sgk_ASC($a,$b) { return self::SortPlayers($a,$b,'sgk','+'); }
  public static function SortPlayers_sgk_DESC($a,$b) { return self::SortPlayers($a,$b,'sgk','-'); }

  /**
   * Helper Function
   * Can be used to created a link to sort data accordingly.
   * @param [type] $_by    [description]
   * @param [type] $sby    [description]
   * @param [type] $soby   [description]
   * @param [type] $soby2  [description]
   * @param [type] $stitle [description]
   */
  function LinkImageSort($_by,$sby,$soby,$soby2,$stitle) {
   if($_by==$sby)
    return <<<EOF
  <a href="{$_SERVER['PHP_SELF']}?sort={$soby}&amp;by={$sby}" class="formfont" onmouseover="if(document.getElementById('so{$sby}')){ document.getElementById('so{$sby}').src='./swat4query/images/server_{$soby}.gif'; }" onmouseout="if(document.getElementById('so{$sby}')){ document.getElementById('so{$sby}').src='./swat4query/images/server_{$soby2}.gif'; }"><b>{$stitle}</b> <img src="./swat4query/images/server_{$soby2}.gif" width="11" height="9" border="0" alt="{$soby}" id="so{$sby}" />
EOF;
   else return '<a href="'.$_SERVER['PHP_SELF'].'?sort='.$soby.'&amp;by='.$sby.'" class="formfont"><b>'.$stitle.'</b>';
  }

  /**
   * This functions returns the Hostname of Server.
   * and a default of Swat4 Server if hostname not found.
   */
  function GetServerName() {
   global $servername;
   if(strlen($servername) > 0)
    return $servername;
   else
    return "Swat 4 PHP Server Query";
  }


}