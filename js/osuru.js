var osuAPI="https://sip4.hvcloud.ru/t2/process_h.php";

function httpGetAsync(theUrl,  step)
{
    var xmlHttp = new XMLHttpRequest();
    xmlHttp.onreadystatechange = function() { 
        if (xmlHttp.readyState == 4 && xmlHttp.status == 200)
            setDiv(xmlHttp.responseText,step);
    }
    xmlHttp.open("GET", theUrl, true); // true for asynchronous 
    xmlHttp.send(null);
}

function requestData(step,obj){
 id=obj.value;
 who=(document.getElementById('who')!=null)?document.getElementById('who').value : 0;
 var date='';
 
 var url=osuAPI+'?step='+step+'&who='+who+'&date='+date;
  for(i=1;i<4;i++){
	s=(document.getElementById('s'+i)!=null)?document.getElementById('s'+i).value : 0;
	url=url+"&s"+i+"="+s;
 }
 
 httpGetAsync(url,step+1);
 
}

function setDiv(data,step){
	
	document.getElementById('f'+step).innerHTML=data;
	var s=document.getElementById('s'+step);
	s.setAttribute( "onClick", "javascript:requestData("+step+",this);");
for(i=step+1;i<5;i++){
	if(document.getElementById('f'+i)!=null) document.getElementById('f'+i).innerHTML=""; 
	
 }
 	
}