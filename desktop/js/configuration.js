function renew() {

    if ($('#sel_token').val()=='manu') {
      $('.manu').show();
      $('.auto').hide();
    }
    else {
        $('.auto').show();
      $('.manu').hide();
    }
  }

  function renew1() {

    if ($('#sel_reseau').val()=='tri') {
      $('.tri').show();
      $('.mono').hide();
    }
    else {
        $('.mono').show();
      $('.tri').hide();
    }
  }