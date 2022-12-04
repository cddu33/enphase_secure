function renew() {
    //$('.cf_z2m').hide();
    //$('.cf_mqtt').hide();
    //$('.cf_mqttcli').hide();
    if ($('#sel_token').val()=='manu') {
      $('.manu').show();
      $('.auto').hide();
    }
    else {
        $('.auto').show();
      $('.manu').hide();
    }
  }