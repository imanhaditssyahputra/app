 <footer class="footer">
     <div class="container-fluid">
         <div class="footer-inner">
             e-Trading Managemen System - <script>
                 document.write(new Date().getFullYear())
             </script> &copy; e-Software™| Periode : <?=$periode?> | <?=$convdate?> <span id="jam" style="font-size: 18px;"></span>
         </div>
     </div>
 </footer>
 <script>
    window.onload = function()   {
     show2();         
   }
    //untuk jam
    function show2(){
        if (!document.all&&!document.getElementById)
            return
            thelement=document.getElementById? document.getElementById("jam"): document.all.tick2
            var Digital=new Date()
            var hours=Digital.getHours()
            var minutes=Digital.getMinutes()
            var seconds=Digital.getSeconds()
            var dn="PM"
        
        if (hours<12)
            dn="AM"
        
        if (hours>12)
            hours=hours-12
        
        if (hours==0)
            hours=12
        
        if (minutes<=9)
            minutes="0"+minutes
        
        if (seconds<=9)
            seconds="0"+seconds
        
        var ctime=hours+":"+minutes+":"+seconds+" "+dn
        thelement.innerHTML=""+ctime+""
        setTimeout("show2()",1000)
    }
 </script>