# Makefile

It provides some shortcuts for the most common tasks.
To view all the available commands, run `make`.

Run `make build` to build fresh images
Run `make up` (detached mode without logs)
Run `make down` to stop the Docker containers

If you want to run make from within the `php` container, in the [Dockerfile](/Dockerfile),
add:

```diff
gettext \
git \
+make \
```

And rebuild the PHP image.

**PS**: If using Windows, you have to install [chocolatey.org](https://chocolatey.org/)
or use [Cygwin](http://cygwin.com) to use the `make` command. Check out this
[StackOverflow question](https://stackoverflow.com/q/2532234/633864) for more explanations.
