# IRCBot: PHP IRC Bot Framework

## How it works?

You just have to run like the example below:

<code>
./irc.sh --host=&lt;host&gt; --port=&lt;port&gt; --channel=#&lt;channel name&gt; --attach=&lt;bot name&gt;
</code>

This is help output:
<code>
~$ ./irc.sh --help

IRCBot Framework Copyleft (c) 2010 Fatih Kadir AKIN
This program comes with ABSOLUTELY NO WARRANTY.
For more information, please refer to &lt;http://unlicense.org/&gt;

Usage: ircbot.php [options]

	-s or --server &lt;server&gt;		Server to connect
	-p or --port &lt;port&gt;		Port to connect
	-c or --channel &lt;channel&gt;	IRC Channel name to connect, must be started with "#"
	-a or --attach &lt;bot&gt;		A bot name to attach, stored in IRC/Bot folder.
	-h or --help			Show this information.

</code>

Then your bot logins and runs.

## How can I create a new IRC Bot?

Answer is so easy: Creating a new class extends IRC Bot class. And implement required methods.
And use IRC Bot API.

## Can I use Framework's Client class without IRC Bot?

Sure. You can use IRC Client class without attaching any Bot.

More details are coming soon :)