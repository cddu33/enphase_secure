function renew() {
    //$('.cf_z2m').hide();
    //$('.cf_mqtt').hide();
    //$('.cf_mqttcli').hide();
    var opt = '*'+$('#ctoken').val();
    if (opt=="manu") {
      $('.token').show();
      $('.user').hide();
      $('.password').hide();
      $('.serie').hide();
      $('.site').hide();
    }
    else {
        $('.token').hide();
        $('.user').show();
        $('.password').show();
        $('.serie').show();
        $('.site').show();
    }
  }