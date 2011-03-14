<?php

/*
BU DOSYA ESKİDİR, ircbot.php'ye gözatın!!!!







*/

    //Eğer CLI dışında bir ortamdan girildiyse hata verilir.
    if (php_sapi_name() != 'cli')
    {
        exit('Bu bot yalnızca CLI üzerinde çalışmalıdır.');
    }

    //Değerler CLI'den alınır.
    $options = getopt('h:p:n:c:', array(
        'host:',        //host
        'port:',        //port
        'channel:',    //channel
    ));

    $owner = 'fka';

    $names = array(
        'ramboberk',
        /*'ferda',
        'nevin',
        'gulay',
        'selin',
        'esra',
        'hulya',
        'gul'*/
    );

    include 'answers.php';

    //Eğer gerekli parametreler eksikse hata verilir.
    if (!is_array($options) || count($options) == 0)
    {
        exit("You must define, --host, --port, --channel \n\n");
    }

    set_time_limit(0);
    $irc = fsockopen($options['host'], $options['port'], $error_number, $error_string, 30);
    if (!$irc)
    {
        exit("$error_number: $error_string");
    } else {
        echo "Connected: {$options['host']}:{$options['port']} \n";
    }

    $name = $names[rand(1, count($names))-1];
    //$name = "fka_{$name}_bot";

    fputs($irc, "NICK {$name}\n");
    fputs($irc, "USER {$name} 0 *: {$name}\n");
    fputs($irc, "JOIN {$options['channel']}\n");

    function random_select($array)
    {
        return $array[rand(1, count($array))-1];
    }

    $admin = array(
        'pause' => false
    );
    function admin($message)
    {
        global $irc, $owner, $admin;

        switch (strtolower(trim($message)))
        {
            case 'stop':
                $admin['pause'] = true;
                fputs($irc, sprintf("PRIVMSG %s : %s \n", $owner, 'Paused'));
                break;
            case 'start':
                $admin['pause'] = false;
                fputs($irc, sprintf("PRIVMSG %s : %s \n", $owner, 'Started'));
                break;
        }
        return $admin;
    }

    function greet($nickname)
    {
        global $owner, $name, $options, $answers;

        $answer_command = "PRIVMSG %s : %s \n";

        if ($nickname == $name)
        {
            $answer = random_select($answers['channel']['greetings']['own']);
            $to = $options['channel'];
        } else {
            if ($nickname == $owner)
            {
                $answer = random_select($answers['channel']['greetings']['owner']);
                $to = $options['channel'];
            } else {
                $answer = random_select($answers['channel']['greetings']['to']);
                $to = $options['channel'];
            }
        }

        $answer = strtr($answer, array(
            '%owner%' => $owner,
            '%nick%' => $nickname,
            '%channel%' => $options['channel']
        ));

        $answer_commands = array();
        $answer = array_map('trim', explode('***', $answer));
        foreach ($answer as $answer_text)
            if (isset($to))
                $answer_commands[] = sprintf($answer_command, $to, $answer_text);

        return $answer_commands;
    }

    function answer($nickname, $message, $is_private)
    {
        global $owner, $name, $options, $answers;

        $answer_command = "PRIVMSG %s : %s \n";

        $answer = false;

        if ($is_private)
        {
            if (trim(trim($message, ','), ':') == $name)
            {
                $answer = random_select($answers['private']['direct']);
                $to = $nickname;
            } else {
                $answer = random_select($answers['private']['fuzzy']);
                $to = $nickname;
            }
        } else {
            if (trim(trim($message, ','), ':') == $name)
            {
                $answer = random_select($answers['channel']['direct']);
                $to = $options['channel'];
            } elseif (preg_match('#'.preg_quote($name, '#').'#uis', $message)) {
                $answer = random_select($answers['channel']['fuzzy']);
                $to = $options['channel'];
            }
        }

        $answer = strtr($answer, array(
            '%owner%' => $owner,
            '%nick%' => $nickname,
            '%message%' => $message,
            '%channel%' => $options['channel']
        ));

        $answer_commands = array();
        $answer = array_map('trim', explode('***', $answer));
        foreach ($answer as $answer_text)
            if (isset($to))
                $answer_commands[] = sprintf($answer_command, $to, $answer_text);

        return $answer_commands;
    }

    //listen.
    while (true)
    {
        $message = fgets($irc, 4096);

		echo $message . "\n";

        $time = date('Y-m-d H:i:s');
        if (preg_match('{^:(?<nickname>.*)!.* JOIN.*}uis', $message, $entry)) {

            $entry = array_filter(array_map('trim', $entry));

            $greet = greet($entry['nickname']);

            foreach ($greet as $greet_message)
            {
                sleep(round(strlen($greet_message) / 9, 0));
                fputs($irc, sprintf($greet_message));
            }

        } elseif (preg_match('{^:(?<nickname>.*)!.* PRIVMSG (?<channel>#?[^:]*):(?<message>.*)$}uis', $message, $entry)) {

            $entry = array_filter(array_map('trim', $entry));
            $is_private = false;
            if ($entry['channel'] == $name)
            {
                $is_private = true;
            }
            echo "[{$time}] ".($is_private?'ON PRIVATE':'ON CHANNEL')." <{$entry['nickname']}>: {$entry['message']} \n";

            if ($is_private && $entry['nickname'] == $owner)
            {
                $admin = admin($entry['message']);
                continue;
            }

            if ($admin['pause'] == true)
                continue;

            //cevapla
            $answer = answer($entry['nickname'], $entry['message'], $is_private);

            foreach ($answer as $answer_message)
            {
                sleep(round(strlen($answer_message) / 9, 0));
                fputs($irc, $answer_message);
            }

        } elseif (preg_match('{PING}', $message)) {

            fputs($irc, 'PONG \n');

        } else {

            echo "[{$time}] <IRC> $message";
        }
    }

