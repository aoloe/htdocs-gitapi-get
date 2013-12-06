# gitapi-get

Get files from a Git repository implementing an API compatible with the one provided by Github.

It keeps a local cache in sync with a Git repository by using a public API and downloading the files through HTTP with curl.

# State

I have worked on several specific scripts that query Github and produces HTML that is specific to each project. This repository will contain the code that only queries the git API and syncronizes the local cache. The processing of the files is moved to a separate repository.

There is no working code yet.

# Install



# Configure


# TODO

- integrate the simplejson and mycurl for servers that don't have them enabled
