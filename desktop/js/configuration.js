function renew() {

    if ($('#sel_token').val()=='manu') {
      $('.manu').show();
      $('.auto').hide();
    }
    else {
      $('.auto').show();
      $('.manu').hide();
    }
    if ($('#sel_autoconso').val()=='oui') {
      $('.autoconso').show();
      if ($('#sel_reseau').val()=='tri') {
        $('.tri').show();
        $('.mono').hide();
      }
      else {
        $('.mono').show();
        $('.tri').hide();
      }
    }
    else {
      $('.tri').hide();
      $('.mono').hide();
      $('.autoconso').hide();
    }
  }