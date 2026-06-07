import random
import urllib.parse

width = 400
height = 400

svg = f"<svg width='{width}' height='{height}' viewBox='0 0 {width} {height}' xmlns='http://www.w3.org/2000/svg'><g fill-opacity='0.08'>"

def add_capsule(x, y, angle, scale, color):
    return f"<path d='M-10 -5 A 5 5 0 0 1 10 -5 L 10 5 A 5 5 0 0 1 -10 5 Z' transform='translate({x},{y}) rotate({angle}) scale({scale})' fill='{color}' />"

def add_pill(x, y, angle, scale, color):
    s = f"<circle cx='0' cy='0' r='8' fill='{color}' transform='translate({x},{y}) scale({scale})' />"
    s += f"<line x1='-6' y1='0' x2='6' y2='0' stroke='#ffffff' stroke-width='1.5' transform='translate({x},{y}) rotate({angle}) scale({scale})' />"
    return s

def add_cross(x, y, angle, scale, color):
    return f"<path d='M-4 -10 L4 -10 L4 -4 L10 -4 L10 4 L4 4 L4 10 L-4 10 L-4 4 L-10 4 L-10 -4 L-4 -4 Z' fill='{color}' transform='translate({x},{y}) rotate({angle}) scale({scale})' />"

colors = ['#008080', '#D4AF37', '#20c997']
random.seed(42)

for _ in range(15):
    x = random.randint(0, width)
    y = random.randint(0, height)
    angle = random.randint(0, 360)
    scale = random.uniform(0.6, 1.4)
    color = random.choice(colors)
    svg += add_capsule(x, y, angle, scale, color)

for _ in range(15):
    x = random.randint(0, width)
    y = random.randint(0, height)
    angle = random.randint(0, 360)
    scale = random.uniform(0.6, 1.2)
    color = random.choice(colors)
    svg += add_pill(x, y, angle, scale, color)

for _ in range(8):
    x = random.randint(0, width)
    y = random.randint(0, height)
    angle = random.choice([0, 15, 30, 45, -15, -30, -45])
    scale = random.uniform(0.5, 1.0)
    color = random.choice(colors)
    svg += add_cross(x, y, angle, scale, color)

svg += "</g></svg>"

encoded = 'data:image/svg+xml,' + svg.replace('%', '%25').replace('#', '%23').replace('<', '%3C').replace('>', '%3E').replace(' ', '%20').replace('"', "'")
print(encoded)
