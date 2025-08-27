function str_replace(str){
    return str.replace(/\s+/g, '-');
}
function miliseconds(hrs,min,sec){
    return (hrs*60*60+min*60+sec)*1000;
}
function hmtominuts(h,m){
    var mint = Math.floor(h*60)+Math.round(m);
    return mint;
}
function minuttohm(m,tm){
	var mins = m % 60;
	var hrs = parseInt((m) / 60 );
	if(tm){
		return hrs+':'+mins;
	} else {
		return hrs + 'h ' + mins +'m';
	}
}
function minuttodhm(m){
       
	days = parseInt(m / 1440);
	hrs = parseInt((m % 1440)/60);
	mins = parseInt(m % 60);
	var dutxt = '';
	if(days){
		dutxt += days +'d ';
	} 
	if(hrs){
		dutxt += hrs + 'h ';
	}
	if(mins){
		dutxt += mins + 'm';
	}
	return dutxt;
}
function dateln(d){
	jstamp = new Date(d * 1000);
	var days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
	var Months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
	var d = days[jstamp.getDay()];
	var day = jstamp.getDay();
	var y = jstamp.getFullYear();
	var m = Months[jstamp.getMonth()];
	var date = ("0" + day).slice(-2);
	var sy = y.toString().substr(-2);
	return d+', '+date+' '+m;
} 
function ckno(va){
    var n = /^\d+([\,]\d+)*([\.]\d+)?$/;
    return n.test(va);
}
function cofl(str){

    if(str!=undefined){
    var str = str.toString();
    str = str.replace(/[^\d\.\-]/g, ""); 
    return parseFloat(str);
    }
}
function pwc(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}
function sortresults(arr,key,ob) {
    return arr.slice().sort(function(a, b) {
        x = cofl(a[key]);
        y = cofl(b[key]);
        if (ob=='asc') return ((x < y) ? -1 : ((x > y) ? 1 : 0));
        else return ((x < y) ? 1 : ((x > y) ? -1 : 0));
    });
}
function filters(data,kv,cp){ 
    result = data.filter(function (o) {
        return Object.keys(kv).every(function (ke) {
        return kv[ke].some(function (f) {

          if(ckno(f)==true){
            var k = cofl(o[ke]);
            var v = cofl(f);
          } else{
            var k = o[ke].toLowerCase();
            var v = f.toLowerCase();
          }
            switch(cp){
            case '=':
            return k == v;
            break;
            case '<':
            return k <= v;
            break;
            case '>':
            return k >= v;
            break;
            case '<>':
            return (k <= cofl(kv[ke][1]) && k >= cofl(kv[ke][0]));
            break;
            case 's':
            return (k.indexOf(v) > -1);
            default:
            return k === v;
            }
        	});
      	});
	});
	return result;
}
function removeduplictes(arr, key) {
    if (!(arr instanceof Array) || key && typeof key !== 'string') {
        return false;
    }

    if (key && typeof key === 'string') {
        return arr.filter((obj, index, arr) => {
         
             return arr.map(mapObj => mapObj[key]).indexOf(obj[key]) === index;
        });

    } else {
        return arr.filter(function(item, index, arr) {
            return arr.indexOf(item) == index;
        });
    }
}
var Small = {
'zero': 0,
'one': 1,
'two': 2,
'three': 3,
'four': 4,
'five': 5,
'six': 6,
'seven': 7,
'eight': 8,
'nine': 9,
'ten': 10,
'eleven': 11,
'twelve': 12,
'thirteen': 13,
'fourteen': 14,
'fifteen': 15,
'sixteen': 16,
'seventeen': 17,
'eighteen': 18,
'nineteen': 19,
'twenty': 20,
'thirty': 30,
'forty': 40,
'fifty': 50,
'sixty': 60,
'seventy': 70,
'eighty': 80,
'ninety': 90,
'hundred' : 100,
'thousand' : 1000,
'million' : 1000000,
'billion' : 1000000000,
};

var a, g;

function text2num(s) {
    a = s.toString().split(/[\s-]+/);
    g = 0;
    a.forEach(feach);
    return g;
}

function feach(w) {
  var x = Small[w];
     g =  x;
}

function datepicker(){
	$('.datepicker').datepicker({
		 dateFormat: 'dd/mm/yy',
		   beforeShow: function (textbox, instance) {
	       var txtBoxOffset = $(this).offset();
	       var top = txtBoxOffset.top;
	       var left = txtBoxOffset.left;
	       
	       if(left<280){
	       	setTimeout(function () {
                   instance.dpDiv.css({
                       //top: top-190, //you can adjust this value accordingly
                       left: left - 90//show at the end of textBox
               });
           	}, 0);
       	   }
       }
	});
}