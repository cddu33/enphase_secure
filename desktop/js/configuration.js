function renew() {

    if ($('#sel_token').val()=='manu') {
      $('.manu').show();
      $('.auto').hide();
    }
    else {
      $('.auto').show();
      $('.manu').hide();
    }

    if ($('#sel_reseau').val()=='tri') {
      $('.tri').show();
      $('.mono').hide();
    }
    else {
      $('.mono').show();
      $('.tri').hide();
    }
    if ($('#sel_autoconso').val()=='oui') {
      $('.autoconso').show();
    }
    else {
      $('.autoconso').hide();
    }
  }