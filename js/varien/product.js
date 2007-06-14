function openLargeImageWin(url, width, height)
{
	win = window.open(url,'largimage','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width='+width+',height='+height+',screenX=150,screenY=150,top=150,left=150');
	win.focus();
}