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
    let instructions = load_input();

    let mut position = Position{depth: 0, horizontal_displacement: 0, aim: 0};
    for instruction in instructions {
        match instruction {
            Instruction::UP(distance) => {
                position.depth -= distance;
                if position.depth < 0 {
                    panic!("Submarines should not be airborne!");
                }
            }
            Instruction::DOWN(distance) => {
                position.depth += distance
            }
            Instruction::FORWARD(distance) => {
                position.horizontal_displacement += distance
            }
        }
    }

    return position.horizontal_displacement * position.depth;
}

fn solve_b() -> i32 {
    let instructions = load_input();

    let mut position = Position{depth: 0, horizontal_displacement: 0, aim: 0};
    for instruction in instructions {
        match instruction {
            Instruction::UP(distance) => {
                position.aim -= distance;

            }
            Instruction::DOWN(distance) => {
                position.aim += distance
            }
            Instruction::FORWARD(distance) => {
                position.horizontal_displacement += distance;
                position.depth += distance * position.aim;
                if position.depth < 0 {
                    panic!("Submarines should not be airborne!");
                }
            }
        }
    }

    return position.horizontal_displacement * position.depth;
}

enum Instruction {
    UP(i32),
    DOWN(i32),
    FORWARD(i32)
}

struct Position {
    depth: i32,
    horizontal_displacement: i32,
    aim: i32
}

fn is_example_mode() -> bool {
    let example_mode = env::var("AOC_EXAMPLE_MODE");
    if example_mode.is_err() {
        return false;
    }

    return example_mode.unwrap() == "1"
}

fn load_input() -> Vec<Instruction> {
    let filename = if is_example_mode() { "exampleInput.txt" } else { "input.txt" };
    let mut path = String::from("../");
    path.push_str(filename);

    let contents : String = fs::read_to_string(path)
        .expect("Something went wrong reading the file");

    let mut output: Vec<Instruction> = Vec::new();
    for line in contents.lines() {
        let parts = line.split_once(" ").unwrap();

        let direction = parts.0;
        let distance = parts.1.parse::<i32>().unwrap();

        let instruction = match direction {
            "up" => Instruction::UP(distance),
            "down" => Instruction::DOWN(distance),
            "forward" => Instruction::FORWARD(distance),
            _ => panic!()
        };
        output.push(instruction);
    }

    return output;
}
