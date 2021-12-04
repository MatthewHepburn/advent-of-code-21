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
    let mut binary_strings : Vec<String> = Vec::new();
    load_input(&mut binary_strings);

    let string_length = binary_strings[0].len();

    let mut gamma_rate_parts: Vec<char> = Vec::new();
    let mut epsilon_rate_parts: Vec<char> = Vec::new();

    for i in 0..string_length {
        let mut ones = 0;
        let mut zeros = 0;

        for binary_string in &binary_strings {
            let char = binary_string.chars().nth(i).unwrap();
            if char == '1' {
                ones +=1
            } else {
                zeros += 1
            }
        }

        gamma_rate_parts.push( if ones > zeros { '1' } else { '0' });
        epsilon_rate_parts.push( if ones < zeros { '1' } else { '0' });
    }

    let gamma_rate = binary_chars_to_int(&gamma_rate_parts);
    println!("Gamma rate decimal = {}", gamma_rate);
    let epsilon_rate = binary_chars_to_int(&epsilon_rate_parts);
    println!("Epsilon rate decimal = {}", epsilon_rate);

    return gamma_rate * epsilon_rate;
}

fn binary_chars_to_int(binary_string : &[char]) -> i32 {
    let mut value = 0;
    let mut power_of_two = 1;

    for i in 0..binary_string.len() {
        let char = binary_string[binary_string.len() - 1 - i];
        if char == '1' {
            value += power_of_two;
        }
        power_of_two *= 2;
    }

    return value;
}


fn solve_b() -> i32 {
    println!("Not implemented")
}


fn is_example_mode() -> bool {
    let example_mode = env::var("AOC_EXAMPLE_MODE");
    if example_mode.is_err() {
        return false;
    }

    return example_mode.unwrap() == "1"
}

fn load_input(output : &mut Vec<String>) {
    let filename = if is_example_mode() { "exampleInput.txt" } else { "input.txt" };
    let mut path = String::from("../");
    path.push_str(filename);

    let contents : String = fs::read_to_string(path)
        .expect("Something went wrong reading the file");

    for line in contents.lines() {
        output.push(String::from(line));
    }
}
