var t = 1;

function ret() {
    if (t == 0) {
        // window.history.back();
        location=document.referrer;
    }
    t -= 1;
    setTimeout("ret()", 1000);
}

