package com.example.mindcare.ui.screens

import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.Spacer
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.height
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.selection.toggleable
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material3.Button
import androidx.compose.material3.ExperimentalMaterial3Api
import androidx.compose.material3.ModalBottomSheet
import androidx.compose.material3.Text
import androidx.compose.material3.TextField
import androidx.compose.runtime.Composable
import androidx.compose.runtime.getValue
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.remember
import androidx.compose.runtime.setValue
import androidx.compose.ui.Modifier
import androidx.compose.ui.unit.dp
import com.example.mindcare.model.Mood
import com.example.mindcare.ui.components.MoodSelector

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun CheckInBottomSheet(
    onDismiss: () -> Unit,
    onComplete: (Mood?, List<String>, String) -> Unit
) {
    var selectedMood by remember { mutableStateOf<Mood?>(null) }
    val emotions = listOf("Calm", "Anxious", "Grateful", "Overwhelmed", "Excited", "Tired")
    var selectedEmotions by remember { mutableStateOf<Set<String>>(emptySet()) }
    var notes by remember { mutableStateOf("") }

    ModalBottomSheet(
        onDismissRequest = onDismiss,
        shape = RoundedCornerShape(topStart = 24.dp, topEnd = 24.dp)
    ) {
        Column(
            modifier = Modifier
                .padding(horizontal = 20.dp, vertical = 16.dp)
        ) {
            Text(text = "Quick check-in ✨")
            Spacer(modifier = Modifier.height(12.dp))

            Text(text = "How are you feeling right now?")
            Spacer(modifier = Modifier.height(8.dp))
            MoodSelector(selected = selectedMood, onMoodSelected = { selectedMood = it })

            Spacer(modifier = Modifier.height(16.dp))
            Text(text = "What emotions are present?")

            emotions.chunked(3).forEach { row ->
                androidx.compose.foundation.layout.Row(
                    modifier = Modifier
                        .fillMaxWidth()
                        .padding(top = 6.dp)
                ) {
                    row.forEach { emotion ->
                        val isSelected = emotion in selectedEmotions
                        androidx.compose.material3.AssistChip(
                            onClick = {
                                selectedEmotions = if (isSelected) {
                                    selectedEmotions - emotion
                                } else {
                                    selectedEmotions + emotion
                                }
                            },
                            label = { Text(emotion) },
                            modifier = Modifier
                                .padding(end = 8.dp)
                                .toggleable(
                                    value = isSelected,
                                    enabled = true,
                                    onValueChange = {
                                        selectedEmotions = if (isSelected) {
                                            selectedEmotions - emotion
                                        } else {
                                            selectedEmotions + emotion
                                        }
                                    }
                                )
                        )
                    }
                }
            }

            Spacer(modifier = Modifier.height(16.dp))
            Text(text = "Want to add any context?")
            Spacer(modifier = Modifier.height(6.dp))
            TextField(
                value = notes,
                onValueChange = { notes = it },
                modifier = Modifier
                    .fillMaxWidth()
                    .height(100.dp),
                placeholder = { Text("Write a few words about what's on your mind...") }
            )

            Spacer(modifier = Modifier.height(20.dp))
            Button(
                onClick = {
                    onComplete(selectedMood, selectedEmotions.toList(), notes)
                    onDismiss()
                },
                modifier = Modifier.fillMaxWidth()
            ) {
                Text("Complete check-in")
            }
            Spacer(modifier = Modifier.height(16.dp))
        }
    }
}


