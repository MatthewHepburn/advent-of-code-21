use std::env;
use std::fs;

fn main() {
    let args: Vec<String> = env::args().collect();
    if args[1] == "--solve-a" {
        let solution = solve_a();
        println!("{}", solution)
    } else if args[1] == "--solve-b" {
        let solution = solve_b();
        println!("{}", solution)
    } else {
        println!("Pass either --solve-a or --solve-b")
    }
}

fn solve_a() -> i32 {
    let mut depths = load_input();

    let mut last_depth = depths.remove(0);
    let mut depth_increases = 0;

    for depth in depths {
        if depth > last_depth {
            depth_increases += 1;
        }
        last_depth = depth
    }

    return depth_increases;
}

fn solve_b() -> i32 {
    let depths = load_input();
    let mut windows = depths.as_slice().windows(3);

    let mut last_window_sum = sum(windows.next().unwrap());
    let mut depth_increases = 0;
    for window in windows {
        let window_sum = sum(window);
        if window_sum > last_window_sum {
            depth_increases += 1;
            println!("Window has depth {}, last window was {}", window_sum, last_window_sum);
        }
        last_window_sum = window_sum;
    }

    return depth_increases;
}

fn is_example_mode() -> bool {
    let example_mode = env::var("AOC_EXAMPLE_MODE");
    if example_mode.is_err() {
        return false;
    }

    return example_mode.unwrap() == "1"
}

fn load_input() -> Vec<i32> {
    let filename = if is_example_mode() { "exampleInput.txt" } else { "input.txt" };
    let mut path = String::from("../");
    path.push_str(filename);

    let contents : String = fs::read_to_string(path)
        .expect("Something went wrong reading the file");

    let mut output: Vec<i32> = Vec::new();
    for line in contents.lines() {
        output.push(line.parse::<i32>().unwrap());
    }

    return output;
}

fn sum(slice: &[i32]) -> i32
{
    let mut sum = 0;
    for val in slice {
        sum += val
    }

    return sum;
}
