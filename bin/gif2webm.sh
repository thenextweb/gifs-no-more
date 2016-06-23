#!/bin/bash

set -eu

if (( $# != 1 )); then
    echo "Usage: gif2webm ANIMATED-GIF" 1>&2
    echo 1>&2
    echo "Converts ANIMATED-GIF to a webm file."

    exit 1
fi

hash ffmpeg 2>/dev/null || {
    echo >&2 "Couldn't find ffmpeg. It MUST be installed.  Aborting."; \
    exit 1;
}

INFILE="${1}"
OUTFILE="${INFILE%.gif}.webm"
# Stick the .webm in the same directory as the original .gif, regardless of current directory

# -y will overwrite all existing files without asking
# -movflags +faststart allows the video to start playing before being completely loaded
# livpx-vp9 is not well supported yet and it produces errors on FF (corrupt video) and Chrome (wrong colorspace), newer versions of ffmpeg shoud fix this problem, see https://trac.ffmpeg.org/ticket/5249
ffmpeg -i "${INFILE}" -y -c:v libvpx -an -q:v 1  -movflags +faststart "${OUTFILE}"
