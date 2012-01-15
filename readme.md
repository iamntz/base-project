## Intro

I always hated to create few folders and few files every time I start a project.

Some time ago I created a archive that contain few files and some basic folder structure 
but I quickly find out that is a odd method and too many steps where involved in updating.

And because I just discovered Git, I also just discovered bash prompt. 
So I created a quick bash script with all the stuff I need.

I created two ways of using this: one should be cross platform (bash) and the other one is windows(7) only.


## Bash/Cygwin Way
You just need to copy `baseproject` in your home directory and make a SymLink of i.bash to home directory.
To do so, in opne a bash prompt and type:

`ln ~/baseproject/i.bash ~/i.bash`

That's all! All you have to do now is to right click a directory (on windows, I really don't know how it works on other platforms!) 
and chose "Bash Prompt Here*" then type: `~/i.bash` .

###if you don't have „bash prompt here”, install chere in your cygwin distribution then type:
`chere -i`
in a cygwin console.

All files are created.


## Windows
This is tested _only_ on win7 with powershell installed. This means that I used some commands that _may_ be unavailable by default.
All you need is to copy `i.bat` to `c:\windows` as `baseproject.bat` (or any other name that suits you). 
Then, in a cmd or powershell window, just type `baseproject` (or the name you picked) when you are in your project directory.

That's all!

The script will automatically download all files, so you will always have latest version of files.

_You don't need to clone the repo or any other file from baseproject_

*hint:* if you are right-click on the project directory while you press shift key, you will see `open command window here` (or something similar).

*hint2:* you can use [StExBar](http://tools.tortoisesvn.net/StExBar.html) and add a shortcut directly on windows explorer, like so:
![StExBar](http://content.screencast.com/users/iamntz/folders/Jing/media/fec4ec95-4970-4f67-a05d-c8ff43ce1cc6/2012-01-15_2025.png)

*hint3:* if you are confident enough with regedit (i'm not!) you can tweak it to have `create a base project here` on right click on a directory.
Google should help you ;) (you can start [here](http://www.iamntz.com/1059/frontend-developer/how-to-use-mintty-with-cygwin-by-default))