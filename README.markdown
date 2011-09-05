Renvoi un array avec pour clés : text et date
    jTwitter::get('UserName'); // arguments optionnels : nombre maxi de tweets, si l'on doit effectuer le parsing du contenu

Utilisation classique avec une jZone
    $rep->body->assignZone('foobar', 'jTwitter~timeline', array('user'=>'UserName'));
ou
    {zone 'jTwitter~timeline', array('user'=>'UserName')}