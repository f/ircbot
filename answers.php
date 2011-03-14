<?php
    $answers = array(
        'private' => array(
            'direct' => array(
                'efendim?',
                'kanala gel #%channel%, özelden konuşmuyorum',
                'evet?',
                '??',
                'evt?',
                'eet?',
                'he',
                'buyur',
                '??????'
            ),
            'fuzzy' => array(
                'anlamadım?',
                'ne demek istediğini anlamadım?',
                '??',
                'ya kusura bakma %nick% ne demek istedin?',
                ':)',
                'ne?',
                'nldu'
            ),
            'regexp' => array(
                //todo: implement
            )
        ),
        'channel' => array(
            'greetings' => array(
                'owner' => array(
                    'oo hoşgeldin %owner% :) *** %owner%, yüce işlemci seni korusun.',
                    '%owner%, hoşgeldin üstadım.'
                ),
                'to' => array(
                    'selam %nick% :) *** nasılsın? nasıl gidiyor?',
                    'slmmm %nick% :)',
                    '%nick% slm',
                    '%nick%, mrb :)'
                    ),
                'own' => array(
                    'selam naber millet *** nasıl gidiyo neler yapiyonuz',
                    'hop ben geldim :D *** kimler burda?',
                    'huoop nabiyonuz beyler *** nasıl gidiyo?',
                    'hoop *** nediyonuz?',
                    'aloo naber *** :P',
                    'kimse var mı burda *** :D'
                )
            ),
            'direct' => array(
                'evet söyle %nick%',
                '%nick%, söyle bebeim',
                '%nick%, bebeim?'
            ),
            'fuzzy' => array(
                '%nick%, anlamadım?',
                'pşşt %nick%, demek istediğini anlamadım?',
                '%nick%, ??',
                'ya kusura bakma %nick% ne demek istedin?',
                '%nick%, :)',
                '%nick%, ne?',
                '%nick% nldu'
            ),
        )
    );

