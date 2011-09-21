I decided to reverse engineer the first NeoQuest game in PHP, and since I wanted the repository to be
online somewhere, I've put it here on GitHub. Secretly, I just want an excuse to be playing lots of
NeoQuest.

However, Neopets will likely not be very happy with me doing this. I have a few reasons for thinking
they just wouldn't care about this though:

* NeoQuest is a very old game now. I can't find an exact date, but I definitely remember playing it in
the early 2000's. Neopet's has been around since 1999, so it may have been in existance since then.
* They have NeoQuest 2 out now, but that's also a very, very old game.
* NeoQuest is no longer in the popular games list, and I doubt many people even know about it anymore.
* Since I'm reverse engineering the game, I'll miss a number of the quirks of the game, meaning you
should probably go and play the original if you're serious about playing it.
* I've no interest in making money out of this. I just felt like making an open game, and don't have
much imaginiation.
* I doubt they'll eve notice.
* I like asparagus too.

Despite all that, the assets, and text, and story line, and names, and pretty much everything that is
not code all belong to Neopets. I'm well aware that I'm flagrantly disgregarding copyright laws. If
someone from Neopets every gets in touch with me ( neoquest AT leafcanvas.com )  to ask, I will
immediately switch this repository to a private one.

Just to stress a point I've already made, if you're looking to play NeoQuest, you should definitely
play it on the official website. This doesn't even hold a flame to the wonderful, exansive world
created in the game.

The code however, since I've written it all myself without ever seeing any of the original code is all
public domain. Hopefully over time I'll make this into more of an RPG framework which you can take and
build your own games. I'm really interested in how a browser-based Final Fantasy game might work out,
for instance. If this codebase is ever in a state for you to find useful to make your own game then
*please* charge for it. Pick a price model you want, but I'd suggest a one off payment to unlock the
game.

Of course, you *MUST* remove all Neopets related assets before you can even run a free game. That means
every image and piece of user-facing text that is in this repository.

Some minimum requirements:

Server:

* MySQL (any version)
* PHP 5

Client:

* Same as jQuery's support ( http://docs.jquery.com/Browser_compatibility )