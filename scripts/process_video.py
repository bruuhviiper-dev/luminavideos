#!/usr/bin/env python3
"""
Video Processing Script for Tubiii
Processes videos: converts to mp4 H.264, generates thumbnail, extracts duration
"""

import sys
import json
import subprocess
from pathlib import Path

def get_video_duration(video_path):
    """Extract video duration in seconds using FFmpeg"""
    try:
        result = subprocess.run([
            'ffmpeg', '-i', video_path,
            '-f', 'null', '-'
        ], capture_output=True, text=True)
        
        # Parse duration from output
        output = result.stderr
        if 'Duration:' in output:
            duration_str = output.split('Duration:')[1].split(',')[0].strip()
            hours, minutes, seconds = duration_str.split(':')
            total_seconds = int(hours) * 3600 + int(minutes) * 60 + int(float(seconds))
            return total_seconds
        return 0
    except Exception as e:
        print(f"Error extracting duration: {e}", file=sys.stderr)
        return 0

def generate_thumbnail(video_path, output_path, timestamp=5):
    """Generate thumbnail from video at specified timestamp"""
    try:
        subprocess.run([
            'ffmpeg', '-i', video_path,
            '-ss', str(timestamp),
            '-vf', 'scale=320:180',
            '-vframes', '1',
            output_path,
            '-y'
        ], capture_output=True, check=True)
        return True
    except Exception as e:
        print(f"Error generating thumbnail: {e}", file=sys.stderr)
        return False

def convert_video(input_path, output_path):
    """Convert video to H.264 MP4 format"""
    try:
        subprocess.run([
            'ffmpeg', '-i', input_path,
            '-c:v', 'libx264',
            '-preset', 'medium',
            '-c:a', 'aac',
            '-q:v', '5',
            output_path,
            '-y'
        ], capture_output=True, check=True)
        return True
    except Exception as e:
        print(f"Error converting video: {e}", file=sys.stderr)
        return False

def main():
    if len(sys.argv) < 2:
        print("Usage: python process_video.py <video_path>", file=sys.stderr)
        sys.exit(1)
    
    video_path = sys.argv[1]
    
    if not Path(video_path).exists():
        print(f"Error: Video file not found: {video_path}", file=sys.stderr)
        sys.exit(1)
    
    # Get video duration
    duration = get_video_duration(video_path)
    
    # Generate thumbnail
    thumbnail_path = video_path.replace(Path(video_path).suffix, '_thumb.jpg')
    generate_thumbnail(video_path, thumbnail_path)
    
    # Convert video (optional - only if needed)
    output_path = video_path
    # convert_video(video_path, output_path)
    
    # Return JSON response
    result = {
        'success': True,
        'duration': duration,
        'thumbnail_path': thumbnail_path,
        'video_path': output_path
    }
    
    print(json.dumps(result))

if __name__ == '__main__':
    main()
